# Aset Merek TNY & PARTNERS

`TNY_LOGO.source.svg` adalah salinan sumber logo resmi yang diberikan pemilik project.

Asset runtime di `public/brand` dihasilkan dari sumber tersebut. Optimasi SVG harus
mempertahankan tampilan dan tidak boleh menambahkan script, event handler, external
resource, atau elemen aktif lainnya.

Perintah regenerasi SVG:

```bash
npx svgo --multipass resources/brand/TNY_LOGO.source.svg -o public/brand/logo.svg
```

SHA-256 sumber yang diterima pada 6 September 2026:

`734B2465359E92D918049201885244BBD6B8A74B3278B8260A6644B26010F32B`
