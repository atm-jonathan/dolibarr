#!/bin/bash

#---------------------------------------------------------
# Function to extract the class name from modMonmodule.class.php
#---------------------------------------------------------
get_module_classname() {
    local module_path="$1"
    local module_name
    local class_file
    local class_name

    module_name=$(basename "$module_path")
    module_name="${module_name^}"
    class_file="$module_path/core/modules/mod${module_name}.class.php"

    if [[ -f "$class_file" ]]; then
        class_name=$(grep -Eo "class [a-zA-Z0-9_]+" "$class_file" | awk '{print $2}')
        echo "$class_name"
    else
        echo ""
    fi
}

#---------------------------------------------------------
# Function to update modules (TEST MODE)
#---------------------------------------------------------
update_modules() {
    local dry_run=0
    [[ "$1" == "--dry-run" ]] && dry_run=1

    modules_path="/home/client/dolibarr_test/dolibarr/htdocs/custom"

#    echo "Testing update_modules in $modules_path..."

    for module in "$modules_path"/*; do
        if [ -d "$module/.git" ]; then
            echo "Processing module: $(basename "$module")"

            if [[ $dry_run -eq 0 ]]; then
                cd "$module" || continue
                git fetch --all
            else
#                echo "[DRY-RUN] Would fetch updates for $(basename "$module")"
                echo "[DRY-RUN]"
            fi

            latest_version=$(cd "$module" && git branch -r | grep -E 'origin/([0-9]+\.[0-9]+|[0-9]+\.[0-9]+\.[0-9]+)$' | sort -V | tail -n 1)

            if [ -n "$latest_version" ]; then
                if [[ $dry_run -eq 0 ]]; then
                    git checkout "$latest_version"
                else
#                    echo "[DRY-RUN] Would checkout latest version: $latest_version"
                    echo "[DRY-RUN]"
                fi
            else
                branch=""
                if git show-ref --verify --quiet refs/heads/main; then
                    branch="main"
                elif git show-ref --verify --quiet refs/heads/master; then
                    branch="master"
                fi

                if [[ -n "$branch" ]]; then
                    if [[ $dry_run -eq 0 ]]; then
                        git checkout "$branch"
                    else
#                        echo "[DRY-RUN] Would checkout branch: $branch"
                        echo "[DRY-RUN]"
                    fi
                else
                    echo "No valid branch found for $(basename "$module")!"
                    continue
                fi
            fi

            if [[ $dry_run -eq 0 ]]; then
                git pull
                if [ $? -ne 0 ]; then
                    echo "Git pull failed! Would reset if not in dry-run mode."
                fi
            else
#                echo "[DRY-RUN] Would pull latest changes"
                echo "[DRY-RUN]"
            fi

            # Get module class name
            module_class=$(get_module_classname "$module")
echo "[TEST CLASSNAME] $module_class"
            if [[ -n "$module_class" ]]; then
#                echo "[TEST] Would manage activation for: $module_class"
                echo "[TEST]"
                if [[ $dry_run -eq 0 ]]; then
                    php module_manager.php "$module_class"
                else
                    echo "[DRY-RUN] Would execute: php module_manager.php \"$module_class\""
                fi
            else
                echo "Error: Could not determine class name for module $(basename "$module")"
            fi

            [[ $dry_run -eq 0 ]] && cd - >/dev/null
        else
            echo "Skipping non-git module: $(basename "$module")"
        fi
    done

    echo "Module update test complete."
}

# Execute in test mode
update_modules --dry-run
