#!/bin/sh
set -eu

payload="$(cat)"

# Write-like intent matcher.
w1='apply'
w2='patch'
w3='create'
w4='file'
w5='mcp_github_create_or_update'
w6='mcp_github_push'
write_intent_re="${w1}_${w2}|${w3}_${w4}|${w5}_${w4}|${w6}_${w4}|\"old_str\"|\"new_str\"|\"insert_text\"|\*\*\* (Add|Update|Delete) File:"

sensitive_path_re='(^|[^[:alnum:]_])(vendor/|patches/)'
explicit_request_re='explicit(ly)? requested|requested by user|user requested|approved by user|as requested'

if printf '%s' "$payload" | grep -Eiq "$sensitive_path_re" && printf '%s' "$payload" | grep -Eiq "$write_intent_re"; then
    if printf '%s' "$payload" | grep -Eiq "$explicit_request_re"; then
        cat << 'JSON'
{
  "hookSpecificOutput": {
    "hookEventName": "PreToolUse",
    "permissionDecision": "allow",
    "permissionDecisionReason": "Sensitive path edit is explicitly requested."
  }
}
JSON
    else
        cat << 'JSON'
{
  "systemMessage": "Warning: editing vendor/ or patches/ is high-risk. Proceed only when explicitly requested by the user.",
  "hookSpecificOutput": {
    "hookEventName": "PreToolUse",
    "permissionDecision": "allow",
    "permissionDecisionReason": "Sensitive path edit detected; warning emitted."
  }
}
JSON
    fi
    exit 0
fi

cat << 'JSON'
{
  "hookSpecificOutput": {
    "hookEventName": "PreToolUse",
    "permissionDecision": "allow",
    "permissionDecisionReason": "No sensitive path write detected."
  }
}
JSON
