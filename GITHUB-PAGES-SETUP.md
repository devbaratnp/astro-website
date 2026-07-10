# Publish to GitHub Pages

This package is already configured for GitHub Pages using `.github/workflows/pages.yml`.

## Repository
Recommended repository name: `astroshreehari`

Expected public URL after deployment:

`https://devbaratnp.github.io/astroshreehari/`

## GitHub setup
1. Create a new **public** repository named `astroshreehari`.
2. Upload all files and folders from this package to the repository root.
3. Open **Settings → Pages**.
4. Under **Build and deployment → Source**, choose **GitHub Actions**.
5. Open the **Actions** tab and confirm the Pages workflow finishes successfully.

## Important folder structure

```text
astroshreehari/
├── .github/workflows/pages.yml
├── assets/
├── index.html
├── about.html
├── services.html
├── appointment.html
├── contact.html
├── .nojekyll
└── README.md
```

Do not upload the outer folder as another nested folder.

## Custom domain later
After the default GitHub Pages URL works, add `www.astroshreehari.com` in **Settings → Pages → Custom domain**, then configure the required DNS records at the domain provider.
