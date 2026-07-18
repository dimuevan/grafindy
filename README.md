# DimuOne Child

Build sites in this theme; keep the parent (`dimuone`) untouched.

## How the parent framework behaves under a child

- **Everything in Theme Settings works unchanged** — Optimization (static cache,
  assets, images, security, monitoring), General settings, Snippets. The
  framework loads from `get_template_directory()` (the parent), so activating
  the child changes nothing.
- **Asset loading**: the parent's `Asset_Loader` conventions apply to this theme
  too, and the child wins with parent fallback. Drop files in:
  - `assets/css/global.css`, `assets/js/global.js`
  - `assets/css/templates/{template}.css`, `assets/css/singles/{post_type}.css`,
    `assets/css/archives/{post_type}.css`, `assets/css/pages/{slug}.css`,
    `assets/css/parts/{part}--{variant}.css`, `assets/css/blocks/{ns}--{block}.css`
  Only create a child file when you want to REPLACE the parent's file of the
  same scope — an empty child file overrides the parent with nothing.
- **Templates**: copy any parent template (e.g. `single.php`, `parts/…`) into
  the same path here to override it.
- **Static cache**: theme-agnostic (drop-in + `wp-content/cache/`). After
  changing templates or assets, flush the cache from the admin bar.

## Renaming the brand later

The internal namespace (`DIMU\Boilerplate`) and text domain (`dimu`) can stay —
they are invisible to clients. To rename the visible theme: change
`Theme Name:` in both themes' `style.css` and the parent folder name (update
`Template:` here to match).
# grafindy
