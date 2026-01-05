# Laravel Shadcn UI Kit

A beautiful, Apple-inspired UI kit for Laravel projects with Blade components.

## Features

-   🎨 **shadcn UI inspired design** with Apple.com aesthetics
-   📦 **Reusable Blade Components** (Button, Card, Input, Textarea, Badge, Alert, Select)
-   🖼️ **Admin & Public Layouts** with responsive sidebar and navigation
-   ✨ **Smooth Animations** (fade-up, scale-in, slide-in)
-   🌓 **Dark mode ready** with CSS variables
-   📱 **Mobile responsive**

---

## Quick Installation

### 1. Copy Files

Copy these folders to your Laravel project:

```bash
# CSS (add to your existing app.css or replace it)
cp ui-kit/css/app.css resources/css/app.css

# Blade Components
cp -r ui-kit/components/ui resources/views/components/

# Layouts
cp -r ui-kit/layouts resources/views/components/

# JavaScript (optional Alpine.js utilities)
cp ui-kit/js/app.js resources/js/app.js
```

### 2. Dependencies

Make sure you have these in your `package.json`:

```json
{
    "devDependencies": {
        "@tailwindcss/forms": "^0.5.2",
        "alpinejs": "^3.4.2",
        "tailwindcss": "^3.1.0"
    }
}
```

Then run:

```bash
npm install
npm run build
```

### 3. Include Alpine.js

In your `resources/js/app.js`:

```javascript
import Alpine from "alpinejs";
window.Alpine = Alpine;
Alpine.start();
```

---

## Usage

### Buttons

```blade
{{-- Primary Button --}}
<x-ui.button type="submit">Save</x-ui.button>

{{-- Variants --}}
<x-ui.button variant="secondary">Cancel</x-ui.button>
<x-ui.button variant="outline">Edit</x-ui.button>
<x-ui.button variant="ghost">Close</x-ui.button>
<x-ui.button variant="destructive">Delete</x-ui.button>

{{-- Sizes --}}
<x-ui.button size="sm">Small</x-ui.button>
<x-ui.button size="lg">Large</x-ui.button>
```

### Cards

```blade
<x-ui.card>
    <p>Simple card content</p>
</x-ui.card>

{{-- With header and footer --}}
<x-ui.card>
    <x-slot:header>
        <h3>Card Title</h3>
    </x-slot:header>

    <p>Card body content here...</p>

    <x-slot:footer>
        <x-ui.button>Action</x-ui.button>
    </x-slot:footer>
</x-ui.card>
```

### Form Inputs

```blade
{{-- Text Input --}}
<x-ui.input
    name="email"
    label="Email Address"
    type="email"
    placeholder="you@example.com"
    :value="old('email')"
    :error="$errors->first('email')"
/>

{{-- Textarea --}}
<x-ui.textarea
    name="message"
    label="Message"
    rows="5"
    placeholder="Your message..."
    :error="$errors->first('message')"
>{{ old('message') }}</x-ui.textarea>

{{-- Select --}}
<x-ui.select
    name="category"
    label="Category"
    :options="['frontend' => 'Frontend', 'backend' => 'Backend']"
    placeholder="Select a category"
/>
```

### Badges

```blade
<x-ui.badge>Default</x-ui.badge>
<x-ui.badge variant="secondary">Secondary</x-ui.badge>
<x-ui.badge variant="success">Success</x-ui.badge>
<x-ui.badge variant="warning">Warning</x-ui.badge>
<x-ui.badge variant="destructive">Error</x-ui.badge>
<x-ui.badge variant="outline">Outline</x-ui.badge>
```

### Alerts

```blade
<x-ui.alert type="success">Operation completed!</x-ui.alert>
<x-ui.alert type="error">Something went wrong.</x-ui.alert>
<x-ui.alert type="warning">Please review your input.</x-ui.alert>
<x-ui.alert type="info" :dismissible="true">This can be dismissed.</x-ui.alert>
```

### Layouts

```blade
{{-- Admin Layout with Sidebar --}}
<x-layouts.admin>
    <x-slot:header>Dashboard</x-slot:header>

    {{-- Your admin content here --}}
</x-layouts.admin>

{{-- Public Layout with Apple-style nav --}}
<x-layouts.public>
    <x-slot:title>Page Title</x-slot:title>

    {{-- Your public content here --}}
</x-layouts.public>
```

---

## CSS Classes

### Utility Classes

```html
<!-- Glass Effect -->
<div class="glass">Glassmorphism</div>
<div class="glass-subtle">Subtle glass</div>

<!-- Gradients -->
<div class="gradient-hero">Hero gradient</div>
<div class="gradient-mesh">Mesh gradient</div>

<!-- Animations -->
<div class="animate-fade-up">Fade up</div>
<div class="animate-scale-in">Scale in</div>
<div class="animate-slide-in-right">Slide right</div>
<div class="animate-float">Float</div>

<!-- Animation Delays -->
<div class="animate-fade-up delay-100">100ms delay</div>
<div class="animate-fade-up delay-200">200ms delay</div>

<!-- Layout -->
<div class="container-tight">Narrow container</div>
<div class="section-padding">Section with padding</div>
```

### Component Classes

```html
<!-- Cards -->
<div class="card">Standard card</div>
<div class="card-premium">Premium card</div>

<!-- Buttons (non-component) -->
<button class="btn btn-primary">Primary</button>
<button class="btn btn-outline btn-sm">Small outline</button>

<!-- Form Elements -->
<input class="input" type="text" />
<textarea class="textarea"></textarea>

<!-- Timeline -->
<div class="timeline">
    <div class="timeline-item">Item 1</div>
    <div class="timeline-item">Item 2</div>
</div>

<!-- Skill Bar -->
<div class="skill-bar">
    <div class="skill-bar-fill" style="width: 80%"></div>
</div>

<!-- Project Card -->
<div class="project-card">
    <div class="project-card-image"><img src="..." /></div>
    <div class="p-4">Content</div>
</div>
```

---

## Customization

### CSS Variables

Edit these in your CSS to customize colors:

```css
:root {
    --primary: 240 5.9% 10%;
    --primary-foreground: 0 0% 98%;
    --secondary: 240 4.8% 95.9%;
    --accent: 240 4.8% 95.9%;
    --destructive: 0 84.2% 60.2%;
    --border: 240 5.9% 90%;
    --radius: 0.5rem;
}
```

---

## License

MIT - Feel free to use in any project!
