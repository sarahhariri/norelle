<nav class="admin-nav" aria-label="Admin navigation">
    <a href="{{ route('admin.orders.index') }}" class="{{ request()->routeIs('admin.orders.*') ? 'active' : '' }}">
        Orders
    </a>

    <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
        Dashboard
    </a>

    <a href="{{ route('admin.products.index') }}" class="{{ request()->routeIs('admin.products.*') ? 'active' : '' }}">
        Products
    </a>

    <a href="{{ route('admin.categories.index') }}"
        class="nav-link {{ request()->routeIs('admin.categories.*') ? 'active' : '' }}">
        Categories
    </a>

    <form method="POST" action="{{ route('admin.logout') }}">


        <button type="submit">
            Sign out
        </button>
    </form>
</nav>
