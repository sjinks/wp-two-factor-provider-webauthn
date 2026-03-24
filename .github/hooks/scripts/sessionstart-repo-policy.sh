#!/bin/sh
set -eu

cat << 'JSON'
{
  "continue": true,
  "systemMessage": "Repo policy: edit source files (not generated outputs), keep WebAuthn payloads unmodified before validation, and avoid references to untracked files in instructions."
}
JSON
