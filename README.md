Chamilo LMS
=============

A next generation Learning Management system focused on ease of use, collaboration and sharing.
See documentation/index.html for a complete overview of Chamilo.

Versions differentiation
------------------------

Beware that the Chamilo Association hosts *two completely different applications*: Chamilo LMS (this software here) and Chamilo LCMS (a more experimental application focused on sharing learning objects, mostly, hosted on Bitbucket, not here).
https://campus.chamilo.org, https://stable.chamilo.org and the vast majority of Chamilo installations around the world (98%) use Chamilo LMS.

Inside this Chamilo LMS project itself, there are two main "branches":
* Chamilo LMS 1.9.x offers a stable version of Chamilo that is made more stable with each new version
* Chamilo LMS HEAD (the default if you download it from Github) is in active development and will soon spawn the new 1.10 version (or v10) of Chamilo LMS. It is NOT to be used in production right now.

If you are in search of the latest patches to your production installation, you should choose 1.9.x. If you are adventurous and look forward to contribute to something that *mostly* works but is still under heavy development, you should stick with the default HEAD branch.

Chamilo LMS v10 should be available in beta version around early 2015, so not too far away, and comes with an improved files structure and a lot of new dependencies/packages coming from Symfony and Composer. If you have time on your hands and are looking for long term contributions, that's where we'd like you to help.

Installation Chamilo v1.9.x
------------

You need a working web server + PHP + MySQL setup.

To install from Git (which means installing an unstable, development version of this application), do the following:

* Create a directory where you will store the Chamilo LMS files (beware, the Git repo is about 1GB in size now)
* Install git (if that's not already the case)
* Clone the Chamilo LMS repo from Github:
```
git clone https://github.com/chamilo/chamilo-lms.git the-directory-you-created
```

Once you have downloaded it, you will need to follow the installation instructions. You can get the latest version inside the documentation/ folder of your recently-downloaded Chamilo, or you can see them online for the latest *stable* version at https://stable.chamilo.org/documentation/installation_guide.html

Before you start the installation procedure, if you want to work on Chamilo LMS 1.9.x, you'll need to do this:
```
cd the-directory-you-created
git checkout --track origin/1.9.x
git config --global push.default current
```

This way, you'll stick to the 1.9.x branch only in this directory (your installation will be 1.9.x only), and when sending commits, they will automatically be sent to the 1.9.x branch.

Finally, if you are really looking into contributing back to Chamilo, you should (really) create yourself a Github account and "fork this project". You would then download Chamilo from your own Github repository first, then send changes to your repository and finally (once you're sure they're matching our coding conventions), submit a "Pull request" to Chamilo. This is the cleanest, more time-saving way to do it!

Installation Chamilo v10
------------

This version is *not* stable, is not even alpha is only for developers and
testing.

Via Command Line:

```
git clone https://github.com/chamilo/chamilo-lms.git chamilo
cd chamilo
composer update
php app/console chamilo:install --force --drop-database
```

Via web:
```
git clone https://github.com/chamilo/chamilo-lms.git chamilo
cd chamilo
composer update
```

Go in your browser to localhost/chamilo/install.php and follow the instructions.


Reporting bugs
--------------

Please submit any bugs, feature requests and non-trivial patches to
http://support.chamilo.org/
Always make sure you look for the Chamilo LMS subproject when submittingbug reports, contributing, asking on the forum, IRC, etc.

Contributing
------------

When contributing patches (which we always welcome, as long as you agree to do that under the GNU/GPLv3 license), please ensure you respect our coding conventions: https://support.chamilo.org/projects/1/wiki/Coding_conventions (mostly PSR-2 with a few additional rules and hints).

Before you contribute, you should consider carefully the branch to which you want to contribute. The "master" branch (the default) is the continuously experimental branch of Chamilo, so by nature it is unstable and it is *not* used in production. The "1.9.x" branch (or the highest number ending with an ".x") is the currently stable branch. New releases are *tags* that are set on the stable branch when a new version is released. So, if you are looking to contribute on a bug of 1.9.8 in prevision for 1.9.9, you should use branch 1.9.x.

We gladly welcome Pull Requests on GitHub, so if you feel like you have 30 minutes and can contribute a patch, fork our repo, create a branch and send a PR (probably against branch 1.9.x). We will review it before the next release. Although we are generally fast enough at reviewing PRs, sometimes we might be more busy than others, so please be patient with us. Ultimately, we *will* review your PR and include it if it's useful and it follows our coding conventions (see link above).

Manual testing
--------------

You can always check the impact of your changes and confirm with other users on the following portals, which are automatically updated every 15 minutes:
* https://stable.chamilo.org for versions 1.9.x
* https://unstable.chamilo.org for development version (currently 1.10) - this one doesn't automatically apply database changes, so it is more likely to break often
These are *NOT* production portals. Your content *WILL* be deleted once every now and then. It is completely public and anyone can enter and delete your content if they want to. DO NOT put important content there.

Automated testing
-----------------

We have a few automated tests written in SimpleTest but, after a series of unsuccessful attempts at developing the right set of tests covering 100% of the code, we decided to give up and rewrite an important part of Chamilo's legacy code. This is what v10, our current master branch, is about (between other things).

You can find the existing tests in the tests/ directory in any clone generated from GitHub (you won't find it in the downloadable archive on our website, though).

Learn more
----------

For news, events and more information on Chamilo LMS please visit
http://www.chamilo.org/

Community
----------

Check out #chamilo on irc.freenode.net.

Visit the official Chamilo Forum: http://www.chamilo.org/forum

License
----------

Chamilo is licensed under the GPLv3 license.

Misc
----

[![Build Status](https://api.travis-ci.org/chamilo/chamilo-lms.png)](https://travis-ci.org/chamilo/chamilo-lms)
