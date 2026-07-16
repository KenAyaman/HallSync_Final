@props(['crumbs'])
{{--
  Usage:
  <x-admin-breadcrumb :crumbs="[
      ['label' => 'User Directory', 'route' => 'admin.users'],
      ['label' => $user->name],
  ]" />
--}}
<nav class="admin-breadcrumb" aria-label="Breadcrumb">
    <ol>
        @foreach($crumbs as $index => $crumb)
            <li>
                @if(isset($crumb['route']))
                    <a href="{{ isset($crumb['params']) ? route($crumb['route'], $crumb['params']) : route($crumb['route']) }}">
                        {{ $crumb['label'] }}
                    </a>
                @else
                    <span aria-current="page">{{ $crumb['label'] }}</span>
                @endif
                @if(!$loop->last)
                    <span class="admin-breadcrumb-sep" aria-hidden="true">/</span>
                @endif
            </li>
        @endforeach
    </ol>
</nav>

<style>
.admin-breadcrumb {
    margin-bottom: 6px;
}
.admin-breadcrumb ol {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    gap: 4px;
    margin: 0;
    padding: 0;
    list-style: none;
}
.admin-breadcrumb li {
    display: inline-flex;
    align-items: center;
    gap: 4px;
}
.admin-breadcrumb a {
    color: #b47721;
    font-size: 0.74rem;
    font-weight: 700;
    text-decoration: none;
}
.admin-breadcrumb a:hover {
    text-decoration: underline;
}
.admin-breadcrumb span[aria-current] {
    color: #786b60;
    font-size: 0.74rem;
    font-weight: 600;
}
.admin-breadcrumb-sep {
    color: #b9a998;
    font-size: 0.74rem;
}
</style>
