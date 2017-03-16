TAGS_FILES = TAGS TAGS-sql TAGS-php
TAGS_FILES_BIG = TAGS-big TAGS-sql-big TAGS-php-big
FIND_NOTS_META = -not \( -name Limbo -prune \) -not \( -name NGender -prune \)
FIND_NOTS_BIG = $(FIND_NOTS_META) -not \( -name test -prune \) -not \( -name lang -prune \)
FIND_NOTS = $(FIND_NOTS_BIG) -not \( -name installer -prune \)
all:  $(TAGS_FILES)
TAGS-all:
	rm -f $(TAGS_FILES)
	make $(TAGS_FILES)
TAGS-all-big:
	rm -f $(TAGS_FILES_BIG)
	make $(TAGS_FILES_BIG)
TAGS:
	etags -o $@ $$(find . $(FIND_NOTS) -name '*.sql' -o -name '*.php' )
TAGS-sql:
	etags -o $@ $$(find . $(FIND_NOTS) -name '*.sql' )
TAGS-php:
	etags -o $@ $$(find . $(FIND_NOTS) -name '*.php' )
sql-files:
	find . $(FIND_NOTS) -name '*.sql' | sed 's/^\.\///' | tee NGender/Analyses/sql-files.list | wc -l
sql-more-files:
	find . $(FIND_NOTS_BIG) -name '*.sql' | sed 's/^\.\///' | tee NGender/Analyses/sql-more-files.list | wc -l
sql-most-files:
	find . $(FIND_NOTS_META) -name '*.sql' | sed 's/^\.\///' | tee NGender/Analyses/sql-most-files.list | wc -l
TAGS-big:
	etags -o $@ $$(find . $(FIND_NOTS_BIG) -name '*.sql' -o -name '*.php' )
TAGS-sql-big:
	etags -o $@ $$(find . $(FIND_NOTS_BIG) -name '*.sql' )
TAGS-php-big:
	etags -o $@ $$(find . $(FIND_NOTS_BIG) -name '*.php' )
php-files:
	find . $(FIND_NOTS) -name '*.php' | sed 's/^\.\///' | tee NGender/Analyses/php-files.list | wc -l
php-more-files:
	find . $(FIND_NOTS_BIG) -name '*.php' | sed 's/^\.\///' | tee NGender/Analyses/php-more-files.list | wc -l
php-most-files:
	find . $(FIND_NOTS_META) -name '*.php' | sed 's/^\.\///' | tee NGender/Analyses/php-most-files.list | wc -l
backup-tiki:
	NGender/MySQL/mysql-backup tiki
backup-all:
	NGender/MySQL/mysql-backup
