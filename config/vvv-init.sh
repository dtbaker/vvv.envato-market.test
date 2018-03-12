#!/bin/bash

set -e
shopt -s expand_aliases
alias wp="wp --allow-root"
WP_CORE_ZIP=https://wordpress.org/nightly-builds/wordpress-latest.zip

# Fixes WP-CLI so it can be used inside this setup on the latest VVV
fixwpcli() {
	local current_dir=$(echo $(pwd))

	if hash wp 2>/dev/null; then
		wp --version
	else
		echo 'Fixing wp-cli...'

		cd /srv/www/wp-cli

		composer update --no-dev
		wp --version

		cd "${current_dir}"
	fi
}

cd ..
printf "Setting up: Envato Market plugin\n"

if [ ! -e wp-config-local.php ]; then
	echo -e '<?php \n/* You can define any overrides you want right here. */\nrequire_once "config/wp-config-vvv.php";' > wp-config-local.php
fi

# Export required PHP constants into Bash.
eval $(php -r '
	require_once "wp-config-local.php";
	foreach( explode( " ", "DB_NAME DB_HOST DB_USER DB_PASSWORD DB_CHARSET" ) as $key ) {
		echo $key . "=" . constant( $key ) . PHP_EOL;
	}
')

# Make a database, if we don't already have one.
mysql -u root --password=root -e "CREATE DATABASE IF NOT EXISTS $DB_NAME CHARACTER SET $DB_CHARSET;"
mysql -u root --password=root -e "GRANT ALL PRIVILEGES ON $DB_NAME.* TO $DB_USER@localhost IDENTIFIED BY '$DB_PASSWORD';"

db_populated=`mysql -u root -proot --skip-column-names -e "SHOW TABLES FROM $DB_NAME"`
if [ "" == "$db_populated" ] && [ -e database/envato.sql ]; then
	echo "Loading envato.sql"
	mysql -u $DB_USER -p$DB_PASSWORD $DB_NAME < database/envato.sql
fi

if [ ! -e wp-cli.local.yml ]; then
	echo -e "path: docroot/\nurl: http://vvv.envato-market.test/" > wp-cli.local.yml
fi

git config core.fileMode false # usually we're not committing executable files

if [ ! -e .git/hooks/pre-commit ] && [ -e dev-lib/pre-commit ]; then
	echo "Install pre-commit hook"
	cd .git/hooks
	if ! ln -s ../../dev-lib/pre-commit .; then
		echo "Failed to create symlink for pre-commit hook, so falling back to copy"
		cp ../../dev-lib/pre-commit .
	fi
	cd - > /dev/null
fi

fixwpcli

if [ ! -e docroot/wp-settings.php ]; then
	mkdir docroot
	cd docroot
	wp core download
	cd ..
fi

if ! wp core is-installed; then
	wp core install --title="Envato" --admin_user="dev" --admin_password="dev" --admin_email="dev@127.0.0.1"
fi

wp core update "$WP_CORE_ZIP"
npm install
composer update

if [ ! -e docroot/wp-content/plugins/envato-market/envato-market.php ]; then
	cd docroot/wp-content/plugins
	git clone -b develop --recurse-submodules git://github.com/envato/wp-envato-market.git envato-market
    cd envato-market
    composer install
    npm install
    if [ -e .git/hooks/pre-commit ]; then
        rm .git/hooks/pre-commit
    fi;
    # We run the precommit hook through vagrant so that phpcs etc.. work well.
    echo 'grunt precommit' > .git/hooks/pre-commit
	cd ../../../..
fi

# Now add any site-specific activations for themes and plugins
wp plugin activate envato-market
