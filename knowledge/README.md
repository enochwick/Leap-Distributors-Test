# Knowledge base sources

The chat widget answers **only** from:

1. The website content (the curated site search index), and
2. The company documents you place **in this folder** as `.txt` or `.md` files.

## Adding documents

You have PDFs — convert them to text first, then rebuild:

```bash
# From the theme root. Converts each PDF to a .txt file in this folder.
python3 tools/pdf-to-text.py /path/to/your/pdfs/
```

(Or drop `.txt` / `.md` files in here directly.)

Then in WordPress: **Settings → Leap AI → "Rebuild Knowledge Base"**.

## Notes

- The filename becomes the source label shown to the model (e.g. `pricing-guide.txt` → "pricing-guide"), so name files clearly.
- Scanned/image-only PDFs won't extract text — they need OCR first.
- Rebuild any time you change site copy or swap a document; otherwise the chat keeps using the last build.
- `kb.json` (the built index, in `assets/data/`) is generated — no need to edit it by hand.
