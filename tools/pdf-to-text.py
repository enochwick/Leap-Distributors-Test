#!/usr/bin/env python3
"""
Convert company PDFs into plain-text files the chat knowledge base can index.

Usage:
    python3 tools/pdf-to-text.py path/to/pdfs/            # convert a folder of PDFs
    python3 tools/pdf-to-text.py one.pdf two.pdf          # convert specific PDFs

Output: a .txt file per PDF, written into the theme's knowledge/ folder.
Then go to wp-admin → Settings → Leap AI → "Rebuild Knowledge Base".
"""
import os, sys, glob

THEME_DIR = os.path.dirname(os.path.dirname(os.path.abspath(__file__)))
OUT_DIR = os.path.join(THEME_DIR, "knowledge")


def extract(pdf_path):
    # Prefer pdfplumber (better layout), fall back to pdftotext.
    try:
        import pdfplumber
        out = []
        with pdfplumber.open(pdf_path) as pdf:
            for page in pdf.pages:
                out.append(page.extract_text() or "")
        return "\n\n".join(out)
    except Exception:
        import subprocess
        return subprocess.check_output(["pdftotext", "-layout", pdf_path, "-"]).decode("utf-8", "ignore")


def main(args):
    if not args:
        print(__doc__)
        return
    # Expand folders to the PDFs inside them.
    pdfs = []
    for a in args:
        pdfs.extend(glob.glob(os.path.join(a, "*.pdf")) if os.path.isdir(a) else [a])

    os.makedirs(OUT_DIR, exist_ok=True)
    for pdf in pdfs:
        text = extract(pdf).strip()
        if not text:
            print(f"  ! no text extracted from {pdf} (scanned image? needs OCR)")
            continue
        name = os.path.splitext(os.path.basename(pdf))[0]
        out = os.path.join(OUT_DIR, name + ".txt")
        with open(out, "w", encoding="utf-8") as f:
            f.write(text)
        print(f"  ✓ {name}.txt  ({len(text):,} chars)")

    print(f"\nDone. Files in: {OUT_DIR}")
    print('Next: wp-admin → Settings → Leap AI → "Rebuild Knowledge Base".')


if __name__ == "__main__":
    main(sys.argv[1:])
