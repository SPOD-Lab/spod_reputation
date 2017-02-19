##What you get
Plugin is composed by a widget on the User Profile, as a front-end component, that shows a button to view a leaderboard of the most valuable users according to their reputation value.
##How it works
hen the plugin is activated, it evaluates for each user a reputation, depending on how many of some action, described below, other users have done on the user. In other word, reputation is evaluated by the interactions of other users with current user.
In detail, some methrics were found to evaluate reputation:
* like on user's status/post
* other users' post on user wall
* comment with datalet
* personal messages received
* follower
* user's public room data (datalet, post, view)
* datalet based on dataset, cocreated by the user
* cocreation room joined
After the initial evaluation, each time a user's profile is visited, the plugin updates the reputation for that user.
The reputation system is extendable introducing some code into the evaluation.php file that contains a single public method that calls other protected methods, each of one evaluate one of the methrics. The added protected method that extend the evaluation system should have this signature
```
/**
* @param $userId the user's id
* @return $count int An Integer that specify the amount of reputation for the evaluated methric
*/
protected function newMethod ( $userId )
```
Furthermore in the public method of the same class should be called the protected method

##Plugin Dependencies
This plugins uses features of other plugins that must be installed. The required Plugins are:
* Newsfeed
* Mailbox
* SPOD Public Room
* SPOD Private Room
* SPOD CoCreation

To install *SPOD Reputation* plugin:

* Clone this project by following the github instruction on *SPOD_INSTALLATION_DIR/ow_plugins*
* Install the plugin on SPOD by *admin plugins panel*
