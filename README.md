# The Baseplate

A WordPress Full Site Editing (FSE) block theme designed for LEGO-focused content websites. Built with modern Gutenberg blocks, fluid responsive design, and custom blocks packaged as a portable mu-plugin.

![The Baseplate Header](docs/header.png)

## Features

### Theme Implementation
- **Homepage Template** - Hero section, featured posts (News/Reviews), and All Updates grid
- **Single Post Template** - Article layout with sticky Table of Contents sidebar
- **Mobile-Responsive Design** - Full-screen hamburger menu for mobile devices
- **Fluid Spacing** - Smooth scaling across all breakpoints
- **Custom Typography** - Plus Jakarta Sans and Barlow Condensed font families

### Custom Blocks (mu-plugin)
Custom Gutenberg blocks are packaged separately in a mu-plugin for portability:

| Block | Description |
|-------|-------------|
| `table-of-contents` | Dynamic H2/H3 heading extraction with active scroll tracking |
| `dummy-subscribe-form` | Newsletter subscription form for the footer |
| `query-offset` | Query loop offset functionality for post grids |

## Screenshots

### Homepage
Full homepage layout with hero, categorized post sections, and newsletter signup.

![Homepage](docs/homepage.png)

### Single Post
Article page featuring the sticky Table of Contents with scroll tracking, Pros/Cons section, and comments.

![Single Post](docs/single.jpg)

## Requirements

- WordPress 6.1+
- PHP 5.7+
- Node.js (for development)
- Composer (for PHP dependencies)

## Installation

### Fresh WordPress Install

1. Clone this repository as your `/wp-content` directory:
   ```bash
   cd /path/to/wordpress
   rm -rf wp-content
   git clone https://github.com/SnakeO/wp-gutenberg-block-theme-baseplate.git wp-content
   ```

2. Import demo content using the included XML file:
   - Go to **Tools > Import** in WordPress admin
   - Install the WordPress Importer if prompted
   - Upload `thebaseplate.WordPress.xml`
   - Media assets are available in the `post-assets` folder

3. Activate the **FSE Pilot** theme in **Appearance > Themes**

## Development Setup

### Install Dependencies

```bash
# Install PHP dependencies
composer install

# Install Node dependencies
npm install
```

### Build Commands

```bash
# Start development watch mode
npm start

# Build for production
npm run build
```

### Linting & Formatting

#### JavaScript/CSS
```bash
npm run lint:scripts      # Lint all JS files
npm run lint:styles       # Lint all CSS/SCSS files
npm run format:scripts    # Fix JS files
npm run format:styles     # Fix CSS/SCSS files
```

#### PHP
```bash
composer run lint:php     # Lint all PHP files
composer run format:php   # Fix all PHP files
```

## Project Structure

```
wp-content/
├── mu-plugins/
│   └── fse-pilot-blocks/       # Custom Gutenberg blocks
│       ├── src/
│       │   ├── table-of-contents/
│       │   ├── dummy-subscribe-form/
│       │   └── query-offset/
│       └── build/              # Compiled block assets
├── themes/
│   └── fse-pilot/              # Main FSE theme
│       ├── assets/
│       │   ├── css/
│       │   ├── fonts/
│       │   ├── images/
│       │   ├── js/
│       │   └── sass/
│       ├── parts/              # Template parts
│       ├── patterns/           # Block patterns
│       ├── templates/          # Page templates
│       ├── functions.php
│       ├── style.css
│       └── theme.json
├── post-assets/                # Demo content images
├── thebaseplate.WordPress.xml  # Demo content import
├── package.json
└── composer.json
```

## Templates

| Template | Description |
|----------|-------------|
| `index.html` | Homepage with hero and post grids |
| `single.html` | Single post with Table of Contents |
| `archive.html` | Category/tag archive pages |
| `page.html` | Static pages |
| `search.html` | Search results |
| `404.html` | Not found page |

## Template Parts

- `header.html` - Site header with navigation
- `footer.html` - Site footer with newsletter and links
- `hero.html` - Homepage hero section
- `post-meta.html` - Post metadata display

## Testing

1. Create a fresh WordPress instance
2. Import content using `thebaseplate.WordPress.xml`
3. Activate the `fse-pilot` theme
4. Verify:
   - Homepage layout matches the design
   - Navigation works on desktop and mobile (hamburger menu)
   - Single post Table of Contents tracks scroll position
   - Featured posts sections filter by correct category (News, Reviews)

## License

GNU General Public License v2 or later. See [LICENSE](LICENSE) for details.

---

*Media assets are AI-generated, except certain stock photos from Unsplash.*
