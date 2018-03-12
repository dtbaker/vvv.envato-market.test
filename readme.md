# Envato Market Plugin Development

## Dev Setup with Varying Vagrant Vagrants

The site was designed to get up and running quickly via [varying-vagrant-vagrants](https://github.com/Varying-Vagrant-Vagrants/VVV).

So to get started, follow the [First Vagrant Up](https://github.com/Varying-Vagrant-Vagrants/VVV#the-first-vagrant-up) instructions.

Once you can verify that `src.wordpress-develop.dev` is accessible on your local system, then you can proceed to add this envato.com repo:

```bash
cd www
git clone --recursive git@github.com:envato/vvv.envato.com.git envato.com
cd envato.com
```

You can then reboot Vagrant to cause the newly added site to be initialized:

```bash
vagrant reload --provision
```

This will run the logic in [`config/vvv-init.sh`](config/vvv-init.sh), including downloading the WordPress Core files (which are git-ignored) along with creating the database and setting up the WordPress site.

Once this finishes, you should be able to access **[vvv.envato.com](http://vvv.envato.com/)** from your browser. The default WordPress username and password is `dev`.

If commands complain about needing to be run in Vagrant, you first can `vagrant ssh` then `cd /srv/www/envato.com`
to run the command, or do:

```bash
vagrant -c 'cd /srv/www/envato.com && wp some command'
```

You can tail the error log in VVV by invoking the bundled script from your host machine:

```bash
vagrant ssh -c 'logfile=/tmp/php_errors.envato.com.log; sudo touch $logfile; sudo chmod a+w $logfile; tail -f $logfile'
```

Or you can just run the included script [`bin/tail-vvv-error-log.sh`](bin/tail-vvv-error-log.sh) that does the same.
