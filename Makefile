TAGS_FILES = TAGS TAGS-sql TAGS-php
TAGS:
	etags --exclude='*[0-9]to[0-9]*' $$(find * -name '*.sql' -o -name '*.php' )
TAGS-sql:
	etags  --exclude='*[0-9]to[0-9]*' -o $@ $$(find * -name '*.sql')
TAGS-php:
	etags -o $@ $$(find * -name '*.php')
TAGS-all:
	rm -f $(TAGS_FILES)
	make $(TAGS_FILES)
all: TAGS 
