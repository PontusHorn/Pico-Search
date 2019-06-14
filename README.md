# Pico-Search

A plugin for the flat file CMS [Pico](https://github.com/picocms/Pico). Allows you to create a very basic search form
that searches through titles and content of your pages. The search results page filters the `pages` array to only
contain pages matching the search terms.

You can optionally scope the search to only get results from within a certain folder. For example, on the page
`yoursite.com/blog/search/foobar`, the `pages` array will only contain results from pages in the `blog` folder.

Search results can be paginated using a plugin such as [Pico-Pagination](https://github.com/rewdy/Pico-Pagination).
The search plugin should be executed before the pagination plugin (execution order is determined by file name).

## Installation

* **Copy the file `40-PicoSearch.php` to the `plugins` sub-folder of your Pico installation directory.**
* **Add a file named `search.md` to your content root or the sub-folder you want to make searchable.**
  This is your search results page. You can leave it empty of content, but set the `Template` meta tag to a template
  that loops through the pages and displays them. Your `search.md` might look like this:
  
  ```
  /*
  Title: Search results
  Template: search
  */
  ```
* **Add a template file with the name defined in `search.md`.**
  Your template file (`search.twig` in the above example) should contain something like the following section, which
  lists the pages matching the search (substitute `paged_pages` for `pages` if using Pico-Pagination):
  
  ```twig
  <div class="SearchResults">
      {% if pages %}
          {% for page in pages %}
              <div class="SearchResult">
                  <h2><a href="{{ page.url }}">{{ page.title }}</a></h2>
                  {% if page.description %}<p>{{ page.description }}</p>{% endif %}
              </div>
          {% endfor %}
      {% else %}
          <p>No results found.</p>
      {% endif %}
  </div>
  ```

  If you simply want to make your search results page look like your standard page, you may want to edit your theme's `index.twig` file and change `{{ content }}` to `{% block content %} {{ content }} {% endblock %}`. This allows you to extend this base template and reuse all the other parts of it in your search results template:

  ```twig
  {% extends "index.twig" %}
  
  {% block content %}
      {{ parent() }}
      
      <div class="SearchResults">
          <!-- Put the code for your search results here -->
      </div>
  {% endblock content %}
  ```

Now, you should be able to visit for example `yoursite.com/search/foobar` (adjust path accordingly if putting search.md
in a sub-folder) and see the search results for "foobar" listed.

## The search form

How to design your search form is up to you, but here's a very rudimentary example which you can put in a
template file:

```html
<form id="search_form" action="{{ "search"|link }}">
    <label for="search_input">Search the site:</label>
    <input type="search" id="search_input" name="q" />
    <input type="submit" value="Search" />
</form>
<script type="text/javascript">
    // Intercept form submit and go to the search results page directly, avoiding a redirect
    document.getElementById('search_form').addEventListener('submit', function (e) {
        var search_terms = document.getElementById('search_input').value;
        location.href = '{{ "search"|link }}/' + encodeURIComponent(search_terms);
        e.preventDefault();
    });
</script>
```

If you want to put it in a content file, you'll have to adjust the template variables accordingly, ie. instead of `{{ "search"|link }}` you'd use something like `%base_url%?search`.

## Configuration options

You can exclude certain pages from being included in the search results by using the configuration option `search_excludes`.
Set it to an array of pages you'd like to exclude, where each page is specified as its path relative to the content root:

```php
$config['search_excludes'] = ['search', 'some/other/page'];
```
