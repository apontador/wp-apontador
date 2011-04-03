extract-i18n-template:
	find . -iname "*.php" | xargs xgettext -LPHP -k__ -k_e -k_n -j plugin/languages/wp-apontador.pot -o plugin/languages/wp-apontador.pot
update-pt-BR-translation:
	msgmerge -U plugin/languages/wp-apontador-pt_BR.po plugin/languages/wp-apontador.pot
compile-translations:
	msgfmt plugin/languages/wp-apontador-pt_BR.po -o plugin/languages/wp-apontador-pt_BR.mo
