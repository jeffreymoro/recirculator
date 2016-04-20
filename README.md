# The Recirculator

A small website that pulls, at random, information about books that have never circulated (which means many different things!) in the libraries of the Five College Consortium: Amherst, Hampshire, Mount Holyoke, and Smith Colleges, and the University of Massachusetts Amherst. Together, these instutitions' collections exceed nine million individual items. A large number of these items are checked in and out every single day. This website shines a light on all of those that aren't.

# Where we are right now

Super alpha release. This is a working repository of the code; because it relies on a PHP proxy server, the eventual site will live at [https://recirculator.jeffreymoro.com](https://recirculator.jeffreymoro.com), rather than GitHub pages as originally planned. For now, you can see live tests occasionally at [https://recirculator.jeffreymoro.com/test](https://recirculator.jeffreymoro.com/test).

# Goalposts

- Reduce latency of PHP proxy API calls. (The answer might just be "get a better server," but work can happen re: synchronous/asynchronous AJAX calls to at least make *everything* as slow as the API call is!)
- Produce the random ID server-side, rather than client-side, to reduce page size. Right now, the IDs for Amherst College alone are 2.5 MB; client-side would get unsustainable pretty fast (or at least would be the size of a standard *Verge* page...)
- CSS it up! The page is mega barebones. Give it some pizzazz!

# Hello my name is

Jeffrey Moro, Post-Baccalaureate Resident with Five College Digital Humanities. [This is my website](http://jeffreymoro.com) and I tweet at [@jeffreymoro](http://www.twitter.com/jeffreymoro). 
