SPR: A PHP-based blog software

Background: Back in 2006, I had decided to get started on a blog for any time I had thoughts about politics. I'd decided that even though tools like Blogger and WordPress were available to me at that time, I'd want to make it more interesting and code my own software for it. And so Senseless Political Ramblings, or SPR, was born.

Since this also happened to be before the advent of widely available web frameworks (Rails was pretty much in its infancy at this time), I decided to hand-code it without much help from any tools or libraries. I did use mod_rewrite in Apache is a way of removing the url dressing, which made things much more helpful, but this is still very "raw" code and a lot of stuff on here is specific to the site I was working on, even though it likely could be genericized and used for a larger, less-specific project.

There are a LOT of things I would have done differently if I had done this today, including:
1. Using dao's rather than pure escaped SQL queries in code.
2. Better abstraction of HTML from PHP. Use of some kind of template system would be much cleaner and nicer.
3. A few unit tests might have been nice :)
4. I would definitely use nicer regexes for the admin post formatting rather than the ugly char-by-char parsing included right now.
5. A central place to store errors in the database without having it all be dir-specific (this version will generate tons of Errors_SPR.log files everywhere)

Nonetheless, this is an interesting, and I dare say fun, journey into a small-to-medium size app written in PHP. Enjoy!

- Matthew
