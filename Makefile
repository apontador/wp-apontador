extract-i18n-template:
	find . -iname "*.php" | xargs xgettext -LPHP -k__ -k_e -k_n -j plugin/languages/wp-apontador.pot -o plugin/languages/wp-apontador.pot
