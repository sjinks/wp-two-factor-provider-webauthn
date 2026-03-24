#!/bin/sh
set -eu

payload="$(cat)"

# Match direct edits to committed generated asset outputs by changed file paths only.
generated_asset_re='(diff --git a/assets/[^[:space:]\\]+(\.min\.js|\.min\.css|\.map)([[:space:]]|$)|(\+\+\+|---) [ab]/assets/[^[:space:]\\]+(\.min\.js|\.min\.css|\.map)|\*\*\* (Add|Update|Delete) File: [^[:space:]\\]*/assets/[^[:space:]\\]+(\.min\.js|\.min\.css|\.map)|"path"[[:space:]]*:[[:space:]]*"assets\\?/[^"[:space:]\\]+(\.min\.js|\.min\.css|\.map)")'

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
  "systemMessage": "Blocked: direct edits to generated assets (assets/*.min.js, assets/*.min.css, assets/*.map) are not allowed. Edit source files instead and run npm run build.",
  "hookSpecificOutput": {
    "hookEventName": "PreToolUse",
    "permissionDecision": "deny",
    "permissionDecisionReason": "Blocked: direct edits to generated assets (assets/*.min.js, assets/*.min.css, assets/*.map) are not allowed. Edit source files instead and run npm run build."
  }
}
JSON
    exit 0
fi

cat << 'JSON'
{
  "hookSpecificOutput": {
    "hookEventName": "PreToolUse",
    "permissionDecision": "allow",
    "permissionDecisionReason": "No generated-asset edit detected."
  }
}
JSON
