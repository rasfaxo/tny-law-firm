# Aset Merek TNY & PARTNERS

`TNY_LOGO.source.svg` adalah salinan sumber logo resmi yang diberikan pemilik project.

Asset runtime di `public/brand` dihasilkan dari sumber tersebut. Optimasi SVG harus
mempertahankan tampilan dan tidak boleh menambahkan script, event handler, external
resource, atau elemen aktif lainnya.

Perintah regenerasi SVG:

```bash
npx svgo --multipass resources/brand/TNY_LOGO.source.svg -o public/brand/logo.svg
```

SHA-256 sumber pengganti yang diterima pada 7 September 2026:

`4E61D4BC20A079DB64AA6FEFBD4BE0FBC3EE625C3CC1913C3D6DA13594A43404`
