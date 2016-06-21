<?php

/**
 *
 * @author Pontus Horn
 * @link https://pontushorn.me
 * @repository https://github.com/PontusHorn/Pico-Search
 * @license http://opensource.org/licenses/MIT
 */

class PicoSearch extends AbstractPicoPlugin
{

    private $search_area;
    private $search_terms;

    /**
	 * Parses the requested URL to determine if a search has been requested. The search may be
	 * scoped to a folder. An example URL: yourdomain.com/blog/search/foobar/page/2,
	 * which searches the /blog folder for "foobar" and shows the second page of results using
	 * e.g. https://github.com/rewdy/Pico-Pagination.
     *
     * @see    Pico::getBaseUrl()
     * @see    Pico::getRequestUrl()
     * @param  string &$url request URL
     * @return void
     */
    public function onRequestUrl(&$url)
    {
        if (preg_match('/^(.+\/)?search\/([^\/]+)(\/.+)?$/', $url, $matches)) {
            $url = $matches[1] . 'search' . ($matches[3] ?: '');
            $this->search_terms = urldecode($matches[2]);

            if (!empty($matches[1])) {
                $this->search_area = $matches[1];
            }
        }
    }

    /**
     * If accessing search results, filter the $pages array to pages matching the search terms.
     *
     * @see    Pico::getPages()
     * @see    Pico::getCurrentPage()
     * @see    Pico::getPreviousPage()
     * @see    Pico::getNextPage()
     * @param  array &$pages        data of all known pages
     * @param  array &$currentPage  data of the page being served
     * @param  array &$previousPage data of the previous page
     * @param  array &$nextPage     data of the next page
     * @return void
     */
    public function onPagesLoaded(&$pages, &$currentPage, &$previousPage, &$nextPage)
    {
        if ($currentPage && isset($this->search_area) || isset($this->search_terms)) {
            if (isset($this->search_area)) {
                $pages = array_filter($pages, function ($page) {
                    return substr($page['id'], 0, strlen($this->search_area)) === $this->search_area;
                });
            }

            if (isset($this->search_terms)) {
                $pages = array_filter($pages, function ($page) {
                    return (stripos($page['title'], $this->search_terms) !== false)
                        || (stripos($page['raw_content'], $this->search_terms) !== false);
                });
            }
        }
    }
}
