<style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }
    :root {
        --ink: #0a0a0a;
        --ink-contrast: #ffffff;
        --on-accent: #0a0a0a;
        --accent: #1fdd6e;
        --accent-2: #3b5bfd;
        --accent-dim: #d7ffe9;
        --page-bg: #f5f4f0;
        --card-bg: #ffffff;
        --text-gray: #57575c;
        --success: #1fa85a;
        --error: #d92d20;
        --soft-red-bg: #ffe1de;
        --soft-red-text: #9a1c1c;
        --soft-green-bg: #d7ffe9;
        --soft-green-text: #0f5c33;
        --shadow: 4px 4px 0 var(--ink);
        --shadow-sm: 3px 3px 0 var(--ink);
        /* aliases so pages written against the older palette keep working */
        --gray: var(--text-gray);
        --gray-dark: var(--ink);
        --gray-light: var(--page-bg);
        --dark-blue: var(--ink);
        --dark-blue-hover: var(--ink);
        --dark-blue-light: var(--accent-2);
        --white: var(--card-bg);
        --warning: #b7791f;
        --soft-amber-bg: #fdf1d6;
        --soft-amber-text: #7a5710;
        --soft-blue-bg: #dbe4ff;
    }
    :root[data-theme="dark"] {
        --ink: #ede9fb;
        --ink-contrast: #120f24;
        --accent: #9b7bff;
        --accent-2: #5b7cff;
        --accent-dim: #2a2255;
        --page-bg: #120f24;
        --card-bg: #1c1740;
        --text-gray: #a79fd1;
        --success: #34d399;
        --error: #f87171;
        --soft-red-bg: #3a1f2e;
        --soft-red-text: #ff9c9c;
        --soft-green-bg: #1c3a2e;
        --soft-green-text: #6ee7b7;
        --warning: #fbbf24;
        --soft-amber-bg: #3a301a;
        --soft-amber-text: #fcd34d;
        --soft-blue-bg: #1c2650;
    }
    body {
        font-family: 'Courier New', ui-monospace, SFMono-Regular, Menlo, monospace;
        background-color: var(--page-bg);
        color: var(--ink);
        line-height: 1.6;
    }
    a { color: inherit; }

    h1 {
        font-size: 1.75rem;
        font-weight: 800;
        color: var(--ink);
        margin-bottom: 1.5rem;
        text-transform: uppercase;
        letter-spacing: 0.01em;
    }
    h2 {
        font-size: 1.05rem;
        font-weight: 800;
        color: var(--ink);
        margin-bottom: 1rem;
        text-transform: uppercase;
        letter-spacing: 0.02em;
    }
    h3 {
        font-size: 0.95rem;
        font-weight: 800;
        color: var(--ink);
        margin-bottom: 0.75rem;
        text-transform: uppercase;
    }

    /* Cards */
    .card {
        background: var(--card-bg);
        border: 2px solid var(--ink);
        box-shadow: var(--shadow);
        padding: 1.5rem;
        margin-bottom: 1.5rem;
    }

    /* Watchlist-style cards */
    .watch-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 1rem;
    }
    .watch-card {
        border: 2px solid var(--ink);
        padding: 1.1rem;
        background: var(--card-bg);
    }
    .watch-card-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        font-size: 0.85rem;
        font-weight: 700;
        margin-bottom: 0.75rem;
        text-transform: uppercase;
    }
    .watch-card-symbol {
        color: var(--text-gray);
        font-weight: 700;
    }
    .watch-card-price {
        font-size: 1.4rem;
        font-weight: 800;
    }
    .watch-card-remove {
        background: none;
        border: none;
        color: var(--text-gray);
        cursor: pointer;
        font-size: 0.7rem;
        text-decoration: underline;
        padding: 0;
        margin-top: 0.5rem;
        font-family: inherit;
        text-transform: uppercase;
        font-weight: 700;
    }
    .watch-card-remove:hover {
        color: var(--error);
    }

    /* Table */
    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 0.5rem;
    }
    th {
        text-align: left;
        font-size: 0.7rem;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        color: var(--ink-contrast);
        background: var(--ink);
        padding: 0.6rem 0.75rem;
        font-weight: 700;
    }
    td {
        padding: 0.85rem 0.75rem;
        border-bottom: 2px solid var(--ink);
        font-size: 0.85rem;
    }
    tr:last-child td {
        border-bottom: none;
    }
    tr:hover td {
        background-color: var(--accent-dim);
    }
    .price {
        font-variant-numeric: tabular-nums;
        font-weight: 700;
    }

    /* Buttons */
    .btn {
        display: inline-block;
        padding: 0.5rem 1.1rem;
        text-decoration: none;
        border: 2px solid var(--ink);
        cursor: pointer;
        font-size: 0.8rem;
        font-weight: 800;
        font-family: inherit;
        text-transform: uppercase;
        transition: transform 0.08s;
        box-shadow: var(--shadow-sm);
        background: var(--card-bg);
        color: var(--ink);
    }
    .btn:active {
        transform: translate(2px, 2px);
        box-shadow: none;
    }
    .btn-primary {
        background-color: var(--accent);
        color: var(--on-accent);
    }
    .btn-success {
        background-color: var(--accent);
        color: var(--on-accent);
    }
    .btn-danger {
        background-color: var(--soft-red-bg);
        color: var(--soft-red-text);
    }
    .btn-secondary {
        background-color: var(--card-bg);
        color: var(--ink);
    }
    .btn-tab {
        box-shadow: none;
        border-bottom: none;
    }
    .btn-tab.active {
        background: var(--ink);
        color: var(--ink-contrast);
    }

    /* Forms */
    .form-group {
        margin-bottom: 1rem;
    }
    label {
        display: block;
        margin-bottom: 0.5rem;
        font-weight: 700;
        color: var(--ink);
        font-size: 0.85rem;
        text-transform: uppercase;
    }
    input[type="text"],
    input[type="email"],
    input[type="password"],
    input[type="number"],
    input[type="date"],
    input[type="datetime-local"],
    textarea,
    select {
        width: 100%;
        padding: 0.5rem 0.65rem;
        border: 2px solid var(--ink);
        font-size: 0.9rem;
        background: var(--card-bg);
        font-family: inherit;
        color: var(--ink);
    }
    input:focus,
    textarea:focus,
    select:focus {
        outline: none;
        box-shadow: 3px 3px 0 var(--accent);
    }
    .percent-buttons {
        display: flex;
        gap: 0.25rem;
        flex-wrap: wrap;
    }
    .percent-btn {
        padding: 0.25rem 0.5rem;
        font-size: 0.7rem;
        background-color: var(--card-bg);
        border: 2px solid var(--ink);
        cursor: pointer;
        color: var(--ink);
        font-family: inherit;
        font-weight: 700;
    }
    .percent-btn:hover {
        background: var(--accent);
        color: var(--on-accent);
    }

    /* Alerts */
    .alert {
        padding: 0.9rem 1.1rem;
        border: 2px solid var(--ink);
        margin-bottom: 1.25rem;
        font-size: 0.85rem;
        font-weight: 600;
        box-shadow: var(--shadow-sm);
    }
    .alert-success {
        background-color: var(--soft-green-bg);
        color: var(--soft-green-text);
    }
    .alert-error {
        background-color: var(--soft-red-bg);
        color: var(--soft-red-text);
    }
    .alert-warning {
        background-color: var(--soft-amber-bg);
        color: var(--soft-amber-text);
    }
    .empty {
        text-align: center;
        padding: 3rem;
        color: var(--text-gray);
    }

    /* Pills / tags */
    .pill {
        display: inline-block;
        padding: 0.15rem 0.55rem;
        border: 2px solid var(--ink);
        font-size: 0.7rem;
        font-weight: 800;
        text-transform: uppercase;
    }
    .pill-long { background: var(--accent); color: var(--on-accent); }
    .pill-short { background: var(--soft-red-bg); color: var(--soft-red-text); }

    /* Theme toggle button, shared shape across every layout */
    .theme-toggle {
        background: var(--card-bg);
        border: 2px solid transparent;
        color: var(--ink);
        font-family: inherit;
        font-weight: 800;
        font-size: 0.7rem;
        text-transform: uppercase;
        padding: 0.4rem 0.6rem;
        cursor: pointer;
        flex-shrink: 0;
    }
    .theme-toggle:hover {
        border-color: var(--ink);
        background: var(--accent);
        color: var(--on-accent);
    }
</style>
