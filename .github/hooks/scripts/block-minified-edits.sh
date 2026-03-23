#!/bin/sh
set -eu

payload="$(cat)"

# Match direct edits to committed generated asset outputs.
generated_asset_re='assets\/[^"[:space:]]+(\.min\.js|\.min\.css|\.map)'

# Build write-intent regex from fragments to avoid self-trigger loops while editing this hook.
w1='apply'
w2='patch'
w3='create'
w4='file'
w5='mcp_github_create_or_update'
w6='mcp_github_push'
write_intent_re="${w1}_${w2}|${w3}_${w4}|${w5}_${w4}|${w6}_${w4}|\"old_str\"|\"new_str\"|\"insert_text\"|\*\*\* (Add|Update|Delete) File:"

if printf '%s' "$payload" | grep -Eiq "$generated_asset_re" && printf '%s' "$payload" | grep -Eiq "$write_intent_re"; then
    cat << 'JSON'
{
  "hookSpecificOutput": {
    "hookEventName": "PreToolUse",
    "permissionDecision": "deny",
    "permissionDecisionReason": "Direct edits to generated assets (assets/*.min.js, assets/*.min.css, assets/*.map) are blocked. Edit source files and run npm run build instead."
  }
}
JSON
    exit 2
fi

cat << 'JSON'
{
  "hookSpecificOutput": {
    "hookEventName": "PreToolUse",
    "permissionDecision": "allow",
    "permissionDecisionReason": "No blocked generated-asset edit detected."
  }
}
JSON
