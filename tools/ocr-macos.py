#!/usr/bin/env python3
"""
OCR image-based PDFs using macOS's built-in Vision framework (no installs).

Usage: python3 tools/ocr-macos.py knowledge/SomeDoc.pdf
Writes knowledge/SomeDoc.txt
"""
import os, sys, subprocess, tempfile, glob
import Vision
from Foundation import NSURL


def ocr_image(path):
    url = NSURL.fileURLWithPath_(path)
    req = Vision.VNRecognizeTextRequest.alloc().init()
    req.setRecognitionLevel_(1)          # accurate
    req.setUsesLanguageCorrection_(True)
    handler = Vision.VNImageRequestHandler.alloc().initWithURL_options_(url, None)
    ok = handler.performRequests_error_([req], None)
    if not ok:
        return ""
    lines = []
    for obs in (req.results() or []):
        cand = obs.topCandidates_(1)
        if cand and len(cand):
            lines.append(cand[0].string())
    return "\n".join(lines)


def main(pdf):
    theme = os.path.dirname(os.path.dirname(os.path.abspath(__file__)))
    out_dir = os.path.join(theme, "knowledge")
    name = os.path.splitext(os.path.basename(pdf))[0]

    with tempfile.TemporaryDirectory() as tmp:
        # Rasterize each page to PNG at 200 DPI.
        subprocess.run(["pdftoppm", "-r", "200", "-png", pdf, os.path.join(tmp, "page")], check=True)
        pages = sorted(glob.glob(os.path.join(tmp, "page-*.png")))
        text = []
        for i, p in enumerate(pages, 1):
            t = ocr_image(p).strip()
            print(f"  page {i}/{len(pages)}: {len(t):,} chars")
            if t:
                text.append(t)

    full = "\n\n".join(text).strip()
    if not full:
        print("  ! OCR produced no text.")
        return
    out = os.path.join(out_dir, name + ".txt")
    with open(out, "w", encoding="utf-8") as f:
        f.write(full)
    print(f"\n  ✓ {name}.txt  ({len(full):,} chars)")


if __name__ == "__main__":
    if len(sys.argv) < 2:
        print(__doc__)
    else:
        main(sys.argv[1])
