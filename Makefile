TAGS_FILES = TAGS TAGS-sql TAGS-php
TAGS:
	etags $$(find * -name '*.sql' -o -name '*.php' )
TAGS-sql:
	etags -o $@ $$(find * -name '*.sql')
TAGS-php:
	etags -o $@ $$(find * -name '*.php')
TAGS-all:
	rm -f $(TAGS_FILES)
	make $(TAGS_FILES)
all: TAGS 
