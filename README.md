# Entry Shortlink

#### Attach simple ID-based shortlinks to entries in Symphony's publish area.

This extension offers a lightweight and performance-oriented alternative to the existing frontend entry link solutions. It's closely related to the [Link Preview][1] extension as it also creates links dynamically at pageload instead of storing them in the database (the way the [Entry URL Field][2] extension does) but differs in that it only uses the ID of an entry as dynamic part of the URL. This results in faster loading times for publish tables as all frontend links can be generated without having to fetch the complete data of all entries.


## 1. Installation

1. Upload the `/entry_shortlink` folder in this archive to your Symphony `/extensions` folder.
2. Go to **System → Extensions** in your Symphony admin area.
3. Enable the extension by selecting '**Entry Shortlink**', choose '**Enable**' from the '**With Selected…**' menu, then click '**Apply**'.


## 2. Field Settings

When adding this field to a section, the following custom options are available:

1. **Shortlink URL** represents the static part of the URL that will be generated for each entry. The system ID of the entry will automatically get appended to this URL and thus built the final shortlink. Defaults to `/id/`
2. **Shortlink Label** is the text used for the button/links in the publish area.
3. **Display URL in entries table** will show the full shortlink in the publish index tables instead of the shortlink label.


## 3. Shortlink routing

While it's perfectly possible to treat the links generated by this extension as final URLs in your frontend it's more likely you want to follow the extension's name approach and actually use them as **shortened** URLs that will get forwarded to a more beautiful and 'speaking' URL. As the frontend routing aspect isn't covered by this extension you'll need to take care of this yourself – my default setup for routing shortlinks roughly looks like this:

1. Install the [Page Headers][3] extension.
2. Create a page named "_Shortlink_", give it the url-handle `id`, attach the url-parameter `entry-id` and give it the types `headers` and `301`.
3. Create a datasource for each section that uses shortlinks, name it "_Shortlink : Section Name_", filter the `System ID` by the `entry-id`-parameter, include all elements that are needed for building the final URL and attaching the datasource to the _Shortlink_ page.
4. Create a utility that builds full target URLs for all entries/sections that use shortlinks.
5. Insert that URL as a 301-redirect-target into the template of the _Shortlink_ page (as described [here][3]).

There might be other solutions using on of the routing extensions, but I haven't tested that yet.


## 4. Acknowledgements

This extension is a variation of the [Link Preview][1]-extension by [deuxhuithuit][4] and draws a lot of ideas and code from there. Thank you!


[1]: http://symphonyextensions.com/extensions/link_preview/
[2]: http://symphonyextensions.com/extensions/entry_url_field/
[3]: http://symphonyextensions.com/extensions/page_headers/
[4]: https://deuxhuithuit.com/


<footer>
	<sup>
		<br/>
		<a href="http://www.getsymphony.com">Symphony CMS</a>
		<i>•</i>
		<a href="http://symphonyextensions.com">Symphony Extensions</a>
		<i>•</i>
		<a href="https://github.com/symphonists/symphony-extensions-network">Symphony Extensions Network</a>
	</sup>
</footer>
