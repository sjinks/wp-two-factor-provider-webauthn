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

# Files likely to contain translatable strings.
string_source_re='(assets\/[^"[:space:]]+\.ts|inc\/[^"[:space:]]+\.php|views\/[^"[:space:]]+\.php|index\.php)'

# Common i18n markers in PHP/TS payloads.
i18n_marker_re='@wordpress\/i18n|(^|[^[:alnum:]_])__\(|(^|[^[:alnum:]_])_x\(|esc_html__\(|esc_attr__\('

if printf '%s' "$payload" | grep -Eiq "$write_intent_re" \
    && printf '%s' "$payload" | grep -Eiq "$string_source_re" \
    && printf '%s' "$payload" | grep -Eiq "$i18n_marker_re"; then
    cat << 'JSON'
{
  "continue": true,
  "systemMessage": "Translatable strings changed. Refresh translation artifacts with: npm run build && make -C lang all"
}
JSON
    exit 0
fi

cat << 'JSON'
{
  "continue": true
}
JSON
