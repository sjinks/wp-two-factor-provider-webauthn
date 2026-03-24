#!/bin/sh
set -eu

payload="$(cat)"

# Detect edits that touch index.php and include version markers.
index_path_re='index\.php'
version_marker_re='(^|\n)[+-].*(Version:|TFA_WEBAUTHN_VERSION)'

# Write-like intent matcher.
w1='apply'
w2='patch'
w3='create'
w4='file'
w5='mcp_github_create_or_update'
w6='mcp_github_push'
write_intent_re="${w1}_${w2}|${w3}_${w4}|${w5}_${w4}|${w6}_${w4}|\"old_str\"|\"new_str\"|\"insert_text\"|\*\*\* (Add|Update|Delete) File:"

if printf '%s' "$payload" | grep -Eiq "$index_path_re" \
    && printf '%s' "$payload" | grep -Eiq "$version_marker_re" \
    && printf '%s' "$payload" | grep -Eiq "$write_intent_re"; then
    cat << 'JSON'
{
  "continue": true,
  "systemMessage": "index.php version metadata appears to have changed. Confirm README.md and readme.txt release metadata/changelog entries are updated accordingly."
}
JSON
    exit 0
fi

cat << 'JSON'
{
  "continue": true
}
JSON
