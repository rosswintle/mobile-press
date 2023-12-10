/**
 * Initializes the AlpineJS data for the site app.
 */
document.addEventListener('alpine:init', () => {
    // Initialize reusable data in AlpineJS
    Alpine.data('mobilepress', function () {
        return {

            /**
             * The original site domain.
             * @type {string}
             */
            originalSiteUrl: 'https://rosswintle.test',

            /**
             * The current site domain
             * @type {string}
             */
            // currentSiteDomain: window.location.hostname,

            /**
             * The current site origin.
             * @type {string}
             */
            currentSiteUrl: window.location.origin,

            /**
             * The current data.
             * @type {object}
             */
            currentData: null,

            /**
             * The current data.
             * @type {object}
             */
            currentHtml: null,

            /**
             * The current page.
             * @type {string}
             */
            currentPage: '/',

            /**
             * Initializes the Common Prayer app.
             * @returns {Promise<void>}
             */
            async init() {
                // await this.loadPage('/data/index.html', false);
                // await this.loadPage('/data/home/', true);
                this.setWatchHandlersOnLinks();
            },

            /**
             * Loads a page from a given URL and updates the current page and data.
             * If the URL is the home page, it loads the current day.
             *
             * @param {string} url - The URL of the page to load.
             * @param {boolean} isPageLink - Indicates whether the URL is an app link.
             * @returns {Promise<void>} - A promise that resolves when the page is loaded successfully.
             */
            async loadPage(url, isPageLink) {
                // URLs may be links to the original site's domain, so get that replaced
                // with the current domain.

                // Creating a new URL object needs a base otherwise it sometimes fails.
                let path = new URL(url, this.currentSiteUrl).pathname;

                // Special case for home
                if (path === '/') {
                    this.currentPage = '/';
                    this.currentData = null;
                    this.currentHtml = null;
                }

                console.log(`Loading page ${this.currentSiteUrl}${path}`);

                // A pageLink is an in-app link that's direct link to an HTML file.
                // We can just fetch that and load it.
                if (isPageLink) {
                    try {
                        const response = await fetch(`${this.currentSiteUrl}${path}`);
                        const data = await response.text();
                        this.currentPage = path;
                        this.currentData = null;
                        this.currentHtml = data;
                    } catch (error) {
                        console.error(error);
                    }

                    return;
                }

                // Only prepend the data path if it's not already there - Pagefind will have links
                // to the data path already.
                if (! path.startsWith('/data/')) {
                    path = `/data${path}`;
                }
                path = `${this.currentSiteUrl}${path}`;

                if (! path.endsWith('.html')) {
                    // Replace the trailing slash with .json
                    path = path.replace(/\/?$/, '.json');
                }

                console.log(`File is at ${path}`);

                if (path.endsWith('.html')) {
                    try {
                        const response = await fetch(path);
                        const data = await response.text();
                        this.currentPage = path;
                        this.currentHtml = data;
                        this.currentData = null;
                    } catch (error) {
                        console.error(error);
                    }
                } else {
                    try {
                        const response = await fetch(path);
                        const data = await response.json();
                        this.currentPage = path;
                        this.currentHtml = null;
                        this.currentData = data;
                    } catch (error) {
                        console.error(error);
                    }
                }

                // Clear the search box and UI
                document.querySelector('.pagefind-ui__search-clear').click();
            },


            /**
             * Returns the closest link element to the given element, or the element itself
             * if it is a link.
             *
             * @param {HTMLElement} elem - The element to find the closest link element for.
             * @returns {HTMLAnchorElement|null} - The closest link element or null if not found.
             */
            getClosestLinkElement(elem) {
                if (elem.tagName === 'A') {
                    return elem;
                }

                const linkElem = elem.closest('a');

                if (linkElem) {
                    return linkElem;
                } else {
                    return null;
                }
            },

            setWatchHandlersOnLinks() {
                // Get values we need into scope
                const savedThis = this;

                document.body.addEventListener('click', async (event) => {
                    let actualTarget = this.getClosestLinkElement(event.target);

                    if (! actualTarget) {
                        return;
                    }

                    const href = actualTarget.getAttribute('href');

                    if (! href) {
                        return;
                    }

                    // Links to the original site's domain OR the current site's domain
                    // are internal links and should be handled by the app.
                    const url = new URL(href, savedThis.currentSiteUrl);
                    if (
                        url.origin &&
                        ! (url.origin === savedThis.originalSiteUrl ||
                        url.origin === savedThis.currentSiteUrl)
                        ) {
                        return;
                    }

                    const isPageLink = actualTarget.hasAttribute('data-page-link');

                    event.preventDefault();
                    await this.loadPage(href, isPageLink);
                    // Scroll to the top of the page
                    document.documentElement.scrollTop = 0;
                })
            }
        };
    });
})
