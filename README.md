Author: David Christian Liedle <david.liedle@gmail.com>
Original Author: Jeremy Blanchard <auzigog@gmail.com>

This fork's license: MIT Open Source License
Original License: Use it. :)


### Introduction
php_pivotal_tracker is currently a bare-bones implementation of the Pivotal Tracker API in PHP.
It has the core features to do function calls using CURL for requests.
Please send pull requests if you make any additions for projects you have.
The code has not been extensively tested. Use at your own risk.


### What's New
In this fork, the pivotaltracker_rest.php file has been updated to the latest
PHP standards, commented with PHPDoc tags, cleaned up and formatted. The two
classes were moved to their own files in a classes directory, and two interfaces
were created in the interfaces folder.


### TODO
*   TEST THIS FORK! I have just committed the cleanup and refactoring. No tests
    have been done to even see if this will work. USE AT YOUR OWN RISK!
*   Error handling (internally and over REST)
*   Finish implementing all API functions
*   Encoding checks


### Further Reading
[Pivotal Tracker API](http://www.pivotaltracker.com/help/api?version=v3)
[Other language wrappers](http://www.pivotaltracker.com/help/thirdpartytools)
