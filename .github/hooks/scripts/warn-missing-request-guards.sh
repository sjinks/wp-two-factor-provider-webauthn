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

php_path_re='inc\/[^"[:space:]]+\.php'
state_change_re='wp_ajax_|ajax_.*(register|delete|rename|update|create|save)|wp_send_json_success|update_user_meta|delete_user_meta|->(save|update|delete|rename)'
guard_re='check_ajax_referer|current_user_can|verify_nonce|verify_capabilities'

if printf '%s' "$payload" | grep -Eiq "$php_path_re" \
    && printf '%s' "$payload" | grep -Eiq "$write_intent_re" \
    && printf '%s' "$payload" | grep -Eiq "$state_change_re" \
    && ! printf '%s' "$payload" | grep -Eiq "$guard_re"; then
    cat << 'JSON'
{
  "continue": true,
  "systemMessage": "State-changing PHP handler edit detected without obvious nonce/capability guard in the same change. Verify check_ajax_referer/current_user_can (or project wrappers) are present nearby."
}
JSON
    exit 0
fi

cat << 'JSON'
{
  "continue": true
}
JSON
