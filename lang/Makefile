DOMAIN_PHP   := 2fa-wa
DOMAIN_JS    := 2fa-wa-js
PO_FILES_PHP := $(wildcard $(DOMAIN_PHP)-*.po)
PO_FILES_JS  := $(wildcard $(DOMAIN_JS)-*.po)
MO_FILES_PHP := $(patsubst %.po,%.mo,$(PO_FILES_PHP))
JSON_FILES   := $(patsubst %.po,%.json,$(PO_FILES_JS))

POT_PHP      := $(DOMAIN_PHP).pot
POT_JS       := $(DOMAIN_JS).pot

MSGFMT       := msgfmt
MSGMERGE     := msgmerge

.PHONY: all clean

all: $(POT_PHP) $(POT_JS) $(MO_FILES_PHP) $(JSON_FILES)

$(POT_PHP):
	"$(CURDIR)/../vendor/bin/wp" i18n make-pot "$(CURDIR)/../" "$(CURDIR)/../lang/$(POT_PHP)" --domain="$(DOMAIN_PHP)"

$(MO_FILES_PHP): $(PO_FILES_PHP)

%.mo: %.po
	$(MSGFMT) -o$@ $<

%.json: %.po
	npx po2json "$^" > "$@"

$(PO_FILES_PHP): %: $(POT_PHP)
	$(MSGMERGE) -U "$@" $(POT_PHP)
	touch "$@"

$(PO_FILES_JS): %: $(POT_JS)
	$(MSGMERGE) -U "$@" $(POT_JS)
	touch "$@"

clean:
	-rm -f $(POT_PHP) $(MO_FILES_PHP) $(JSON_FILES)
