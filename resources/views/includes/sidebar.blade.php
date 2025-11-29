<div id="sidebar" class="app-sidebar" data-disable-slide-animation="true" data-bs-theme="dark">
    <!-- BEGIN scrollbar -->
    <div class="app-sidebar-content" data-scrollbar="true" data-height="100%">
        <!-- BEGIN menu -->
        <div class="menu">
            <div class="menu-profile">
                <a href="javascript:;" class="menu-profile-link" data-toggle="app-sidebar-profile"
                    data-target="#appSidebarProfileMenu">
                    <div class="menu-profile-cover with-shadow"></div>
                    <div class="menu-profile-image">
                        <img src="/assets/img/favicon.png" alt="" />
                    </div>
                    <div class="menu-profile-info">
                        <div class="d-flex align-items-center">
                            <div class="flex-grow-1">
                                {{ auth()->user()->nama }}
                            </div>
                            <div class="menu-caret ms-auto"></div>
                        </div>
                        <small>{{ auth()->user()->uid }}</small>
                    </div>
                </a>
            </div>
            <div id="appSidebarProfileMenu" class="collapse">
                <div class="menu-item pt-5px">
                    <a href="javascript:;" class="menu-link">
                        <div class="menu-icon"><i class="fa fa-cog"></i></div>
                        <div class="menu-text">Settings</div>
                    </a>
                </div>
                <div class="menu-item">
                    <a href="javascript:;" class="menu-link">
                        <div class="menu-icon"><i class="fa fa-pencil-alt"></i></div>
                        <div class="menu-text"> Send Feedback</div>
                    </a>
                </div>
                <div class="menu-item pb-5px">
                    <a href="javascript:;" class="menu-link">
                        <div class="menu-icon"><i class="fa fa-question-circle"></i></div>
                        <div class="menu-text"> Helps</div>
                    </a>
                </div>
                <div class="menu-divider m-0"></div>
            </div>

            <div class="menu-header">Navigation</div>
            @php
                $currentUrl = '/' . request()->path();
            @endphp
            <div class="menu-item  @if (strpos($currentUrl, '/home') === 0) active @endif">
                <a href="/home" class="menu-link">
                    <div class="menu-icon"><i class="fas fa-dashboard"></i></div>
                    <div class="menu-text">Dashboard
                    </div>
                </a>
            </div>
            @php
                if (!function_exists('renderSubMenu')) {
                    function renderSubMenu($parent, $value, $currentUrl)
                    {
                        $subMenu = '';
                        $GLOBALS['sub_level'] += 1;
                        $GLOBALS['active'][$GLOBALS['sub_level']] = '';
                        $currentLevel = $GLOBALS['sub_level'];
                        foreach (collect($value) as $key => $row) {
                            $GLOBALS['subparent_level'] = '';

                            $url = str_replace(
                                [' ', '&', '\'', '.'],
                                '',
                                strtolower($parent . '/' . str_replace('/', '', $row['title'])),
                            );
                            if (auth()->user()->can(str_replace('/', '', $url))) {
                                $GLOBALS['subparent_level'] = '';

                                $subSubMenu = '';
                                $hasSub = !empty($row['sub_menu']) ? 'has-sub' : '';
                                $hasCaret = !empty($row['sub_menu']) ? '<div class="menu-caret"></div>' : '';
                                $hasTitle = !empty($row['title'])
                                    ? '<div class="menu-text">' . $row['title'] . '</div>'
                                    : '';

                                if (!empty($row['sub_menu'])) {
                                    $GLOBALS['sub_level'] = 1;
                                    $subSubMenu .= '<div class="menu-submenu">';
                                    if ($row['urutkan'] == true) {
                                        $dataSubMenu = collect($row['sub_menu'])->sortBy('title')->all();
                                    } else {
                                        $dataSubMenu = collect($row['sub_menu'])->all();
                                    }
                                    $subSubMenu .= renderSubMenu(
                                        $parent . '/' . $row['title'],
                                        $dataSubMenu,
                                        $currentUrl,
                                    );
                                    $subSubMenu .= '</div>';
                                }

                                $currentUrlArray = explode('/', substr($currentUrl, 1));

                                $active =
                                    implode('/', array_slice($currentUrlArray, 0, $currentLevel + 1)) == $url
                                        ? 'active '
                                        : '';

                                if ($active) {
                                    $GLOBALS['parent_active'] = true;
                                    $GLOBALS['active'][$GLOBALS['sub_level'] - 1] = true;
                                }
                                $subMenu .=
                                    '<div class="menu-item ' .
                                    $hasSub .
                                    ' ' .
                                    $active .
                                    '"><a href="' .
                                    ($hasSub == '' ? '/' . $url : 'javascript:;') .
                                    '" class="menu-link">' .
                                    $hasTitle .
                                    $hasCaret .
                                    '</a>' .
                                    $subSubMenu .
                                    '</div>';
                            }
                        }
                        return $subMenu;
                    }
                }

                foreach (collect($menu)->sortBy('title')->all() as $key => $row) {
                    $url = str_replace([' ', '/', '&', '\'', '.'], '', strtolower($row['title']));
                    if (auth()->user()->can($url)) {
                        $GLOBALS['parent_active'] = '';

                        $hasSub = !empty($row['sub_menu']) ? 'has-sub' : '';
                        $hasCaret = !empty($row['sub_menu']) ? '<div class="menu-caret"></div>' : '';
                        $hasIcon = !empty($row['icon']) ? '<div class="menu-icon">' . $row['icon'] . '</div>' : '';
                        $hasTitle = !empty($row['title']) ? '<div class="menu-text">' . $row['title'] . '</div>' : '';

                        $subMenu = '';

                        if (!empty($row['sub_menu'])) {
                            if ($row['urutkan'] == true) {
                                $dataSubMenu = collect($row['sub_menu'])->sortBy('title')->all();
                            } else {
                                $dataSubMenu = collect($row['sub_menu'])->all();
                            }
                            $GLOBALS['sub_level'] = 0;
                            $subMenu .= '<div class="menu-submenu">';
                            $subMenu .= renderSubMenu($row['title'], $dataSubMenu, $currentUrl);
                            $subMenu .= '</div>';
                        }
                        $active = explode('/', substr($currentUrl, 1))[0] == $url ? 'active ' : '';
                        // $active = empty($active) && !empty($GLOBALS['parent_active']) ? 'active' : $active;
                        echo '<div class="menu-item ' .
                            $hasSub .
                            ' ' .
                            $active .
                            '"><a href="' .
                            ($hasSub == '' ? '/' . $url : 'javascript:;') .
                            '" class="menu-link">' .
                            $hasIcon .
                            $hasTitle .
                            $hasCaret .
                            '</a>' .
                            $subMenu .
                            '</div>';
                    }
                }
            @endphp
            @role(config('app.name') . '-administrator')
                <div class="menu-item">
                    <a href="/logging" target="_blank" class="menu-link">
                        <div class="menu-icon"><i class="fas fa-scroll"></i></div>
                        <div class="menu-text">Log</div>
                    </a>
                </div>
            @endrole
            <!-- BEGIN minify-button -->
            <div class="menu-item d-flex">
                <a href="javascript:;" class="app-sidebar-minify-btn ms-auto" data-toggle="app-sidebar-minify"><i
                        class="fa fa-angle-double-left"></i></a>
            </div>
            <!-- END minify-button -->

        </div>
        <!-- END menu -->
    </div>
    <!-- END scrollbar -->
</div>
<div class="app-sidebar-bg" data-bs-theme="dark"></div>
<div class="app-sidebar-mobile-backdrop"><a href="#" data-dismiss="app-sidebar-mobile" class="stretched-link"></a>
</div>
<!-- END #sidebar -->
