#!/bin/sh
set -eu

payload="$(cat)"

# Detect potential TypeScript source edits under assets/.
assets_ts_re='assets\/[^"[:space:]]+\.ts'

# Write-like intent matcher.
w1='apply'
w2='patch'
w3='create'
w4='file'
w5='mcp_github_create_or_update'
w6='mcp_github_push'
write_intent_re="${w1}_${w2}|${w3}_${w4}|${w5}_${w4}|${w6}_${w4}|\"old_str\"|\"new_str\"|\"insert_text\"|\*\*\* (Add|Update|Delete) File:"

if printf '%s' "$payload" | grep -Eiq "$assets_ts_re" && printf '%s' "$payload" | grep -Eiq "$write_intent_re"; then
    cat << 'JSON'
{
  "continue": true,
  "systemMessage": "Assets TypeScript changes detected. Run npm run build to regenerate committed outputs in assets/."
}
JSON
    exit 0
fi

cat << 'JSON'
{
  "continue": true
}
JSON
