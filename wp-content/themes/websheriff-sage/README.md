# Websheriff Sage theme

Custom WordPress theme developed by **[Websheriff](https://websheriff.nl/)**, built on **[Roots Sage](https://roots.io/sage/)** (Blade, Acorn, Vite). Upstream concepts and APIs are covered in the official Sage documentation—use that as the reference for installation, structure, and Acorn.

## Documentation

- **Sage**: [https://roots.io/sage/docs/](https://roots.io/sage/docs/) (start with installation and configuration)
- **Roots / community**: [https://discourse.roots.io/](https://discourse.roots.io/)
- **Acorn** (Laravel inside WordPress): [https://roots.io/acorn/](https://roots.io/acorn/)

## Requirements

- PHP **8.2+**
- **Composer**
- **Node.js 20+** and npm or Yarn (see `package.json` `engines`)

This project expects a normal WordPress environment with the theme under `wp-content/themes/`. **Advanced Custom Fields (ACF) Pro** is required for block field groups.

## Setup (from this theme directory)

1. Install PHP dependencies:

   ```bash
   composer install
   ```

2. Install front-end dependencies:

   ```bash
   npm install
   ```

   or `yarn`.

3. **Development** (Vite dev server + HMR):

   ```bash
   npm run dev
   ```

4. **Production assets**:

   ```bash
   npm run build
   ```

5. Activate the theme in WordPress (**Appearance → Themes**). If ACF field groups are shipped as JSON, sync them under **ACF → Tools** when prompted.

For Bedrock or other Roots stacks, follow your host project’s theme path and env conventions; the Sage docs above still apply to how this theme is structured and built.
