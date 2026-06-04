# Leap Distributors — Website Draft

A zero-build static draft of leapdistributors.com. No install, no server, no dependencies. Built to be opened, read, and clicked through like a real site.

## How to view it

Easiest way:

1. Open this folder in Finder.
2. Double-click `index.html`.

It opens in your default browser and the rest of the site is linked from there.

Want a cleaner local URL? Drag the folder onto a terminal window and run `python3 -m http.server 8080`, then visit `http://localhost:8080`. Optional, not required.

## What's in the build

10 pages, all locked copy word-for-word from the content bundle, all matching the IA and CTA spec in `ARCHITECTURE.md`.

| Page | File |
|---|---|
| Home | `index.html` |
| Platform (Stride) | `platform.html` |
| For Surgeons | `for-surgeons.html` |
| For Hospitals | `for-hospitals.html` |
| For Manufacturers | `for-manufacturers.html` |
| About | `about.html` |
| Distributors | `distributors.html` |
| News & Insights | `news.html` |
| Careers | `careers.html` |
| Contact | `contact.html` |
| 404 | `404.html` |

Plus a shared `assets/` folder for CSS, JS, the Leap mark, and the brand fonts (Poppins + Aleo) loaded from Google Fonts.

## What to look at

A few moments worth clicking through:

- **Home hero** — stylized Stride case-log running live (the pulsing "Live" dot is the small visual flex)
- **About → Meet the Partners** — click between founders. The Azure indicator slides horizontally and the bio fades to match.
- **About → Better. Together.** — scroll into the section. The two words slide in from opposite sides, staggered.
- **Distributors → Better. Together.** — same motion, narrative version with the strategic body underneath.
- **Contact form** — try submitting empty. Validation errors are written in Leap voice. Submit a valid form and you get the locked "Got it. We'll be in touch shortly!" confirmation.
- **News & Insights cards** — press cards use the `↗` glyph for external links; insight cards use `→` for internal posts. Same card shell.
- **Mobile** — narrow the window. The nav collapses to a hamburger; the founder tabs reflow.

## What's a placeholder

All marked with a `design note:` comment in the HTML so they're easy to spot when reading source.

- **Stride app screenshots / dashboards** — designed-up dark UI panes (case log, scrub sheet, Power BI mock, rep dashboard). Real software when ready.
- **Hero photography on For Surgeons, About, Careers** — typography-led dark hero with a "photograph — [subject]" label. Real photo drops in via the same frame.
- **Trey video** — 16:9 frame with a play glyph and the locked pull-quote underneath. Real video swaps into the same frame.
- **Founder portraits** — gradient tiles with initials. Real photos drop into the same `.founders__portrait` slots.
- **Phone numbers and Houston address** — `[Phone number]` and `[Houston office address]` placeholders. Verify before launch and replace.
- **Press / insight article links** — currently `#`. Wire to real URLs (external for press, internal for insights) when ready.

## What's missing on purpose

Per your direction:

- No `/orders`, `/privacy`, or `/terms` pages.
- Contact form and newsletter signup are UI-only — they don't send anywhere. The form validates and shows the confirmation; nothing gets transmitted. Easy to wire to Klaviyo/CRM later.
- No CMS for the blog. The three insight cards on `news.html` are static HTML for now.

## Voice notes for any new microcopy

If you need to add anything not in the bundle, the rules from `VOICE.md` apply:

- No em dashes. No "here's the thing." No "unlock." No "navigate" or "journey" metaphors.
- Use contractions. Plain English.
- Form validation errors live in `assets/js/main.js` and follow the rules (e.g., "We need an email to reach you.")

## Brand

- **Fonts:** Poppins (Medium for headlines, Light for body) and Aleo (Medium for eyebrows / section labels). From `LDBrandGuidelines-032125 forDigital.pdf`. Loaded from Google Fonts.
- **Colors:** Azure Blue `#2A7DE1`, Dark Teal `#003B4D`, Light Gray `#D8DFE1`, plus a Persimmon Orange accent (`~#E35F2A` — brand PDF had a typo on the hex; replace with the real value when confirmed).
- **Logo:** the "LEAP" mark you provided is paired with a typeset `DISTRIBUTORS®` to respect the brand guideline that the wordmark can't appear alone.

## File structure

```
LD Website/
├── README.md
├── index.html
├── platform.html
├── for-surgeons.html
├── for-hospitals.html
├── for-manufacturers.html
├── about.html
├── distributors.html
├── news.html
├── careers.html
├── contact.html
├── 404.html
└── assets/
    ├── css/
    │   ├── tokens.css          design tokens (color, type, spacing, motion)
    │   ├── base.css            reset + base typography + containers
    │   ├── components.css      nav, footer, buttons, hero, cards, forms
    │   └── placeholders.css    Stride UI, dashboards, founder portraits, video frame
    ├── js/
    │   └── main.js             nav toggle, scroll reveals, founder slider, form interactions, Better. Together. motion
    └── img/
        └── leap-mark.png       LEAP mark (paired with typeset DISTRIBUTORS in CSS)
```

## When you want to swap in real assets

- **Photos:** drop into `assets/img/`, then replace the matching placeholder `<div>` with an `<img>` tag. Search for `design note:` in any HTML file to find every placeholder.
- **Stride app screenshots:** replace any `.ui-frame` block with a single `<img>` of the real screenshot. Same rounded frame, same shadow.
- **Trey video:** replace the `.video-placeholder` div in `for-surgeons.html` with a standard `<video>` element pointing at the file.
- **Real founder photos:** replace the four `.founders__portrait` div tiles with `<img>` tags. Aspect ratio is 4:5.

## Browser support

Modern Chrome, Safari, Firefox, and Edge. Uses standard CSS (Grid, Flexbox, custom properties, `clamp()`, `aspect-ratio`) and vanilla JS — no transpilation. Respects `prefers-reduced-motion`.
