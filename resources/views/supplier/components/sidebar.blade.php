<div class="page-sidebar page-sidebar-fixed scroll">
    <!-- START X-NAVIGATION -->
    <ul class="x-navigation">
        <li class="xn-logo">
            <a href="/admin/dashboard">Qareeb - Super Admin Dashboard</a>
            <a href="#" class="x-navigation-control"></a>
        </li>

        <li class="xn-profile">
            <div class="profile">
                <div class="profile-image">
                    <img src="/qareeb_admins/{{admin()->image}}" alt="Qareeb" style="width: 110px; height: 110px;"/>
                </div>
                <div class="profile-controls">
                    <a href="/admin/profile" class="profile-control-left" title="View Porfile"><span class="fa fa-user"></span></a>
                </div>
            </div>
        </li>

        <li @if(Request::is('admin/dashboard')) class="active" @endif>
            <a href="/admin/dashboard"><span class="fa fa-dashboard"></span><span class="xn-text">Dashboard</span></a>
        </li>

        @if(admin()->hasPermissionTo('View admin'))
            <li @if(Request::is('admin/admins/*')) class="active" @endif>
                <a href="/admin/admins/index"><span class="fa fa-user-secret"></span><span class="xn-text">Admins</span></a>
            </li>
        @endif

        @if(admin()->hasPermissionTo('View category'))
            <li @if(Request::is('admin/categories/*') xor Request::is('admin/category/*')) class="active" @endif>
                <a href="/admin/categories/all"><span class="fa fa-cubes"></span><span class="xn-text">{{ __('language.Categories') }}</span></a>
            </li>
        @endif

        @if(admin()->hasPermissionTo('View provider'))
            <li class="xn-openable @if(Request::is('admin/providers/*') xor Request::is('admin/provider/*')) active @endif" >
                <a href="#"><span class="fa fa-industry"></span><span class="xn-text">Providers</span></a>
                <ul>
                    <li @if(Request::is('admin/providers/active')) class="active" @endif>
                        <a href="/admin/providers/active"><span class="fa fa-check-square"></span><span class="xn-text">Active</span></a>
                    </li>
                    <li @if(Request::is('admin/providers/suspended')) class="active" @endif>
                        <a href="/admin/providers/suspended"><span class="fa fa-minus-square"></span><span class="xn-text">Suspended</span></a>
                    </li>
                </ul>
            </li>
        @endif

        @if(admin()->hasPermissionTo('View company'))
            <li class="xn-openable @if(Request::is('admin/companies/*') xor Request::is('admin/company/*')) active @endif" >
                <a href="#"><span class="fa fa-building"></span><span class="xn-text">{{ __('language.Companies') }}</span></a>
                <ul>
                    <li @if(Request::is('admin/companies/active')) class="active" @endif>
                        <a href="/admin/companies/active"><span class="fa fa-check-square"></span><span class="xn-text">Active</span></a>
                    </li>
                    <li @if(Request::is('admin/companies/suspended')) class="active" @endif>
                        <a href="/admin/companies/suspended"><span class="fa fa-minus-square"></span><span class="xn-text">Suspended</span></a>
                    </li>
                </ul>
            </li>
        @endif

        <li class="xn-openable @if(Request::is('admin/individual/*') xor Request::is('admin/individuals/*')) active @endif" >
            <a href="#"><span class="fa fa-users"></span><span class="xn-text">Individual</span></a>
            <ul>
                <li class="xn-openable @if(Request::is('admin/individual/user*') xor Request::is('admin/individuals/user*')) active @endif" >
                    <a href="#"><span class="fa fa-user"></span><span class="xn-text">{{ __('language.Users') }}</span></a>
                    <ul>
                        <li @if(Request::is('admin/individuals/user/active')) class="active" @endif>
                            <a href="/admin/individuals/user/active"><span class="fa fa-check-square"></span><span class="xn-text">Active</span></a>
                        </li>
                        <li @if(Request::is('admin/individuals/user/suspended')) class="active" @endif>
                            <a href="/admin/individuals/user/suspended"><span class="fa fa-minus-square"></span><span class="xn-text">Suspended</span></a>
                        </li>
                    </ul>
                </li>

                <li class="xn-openable @if(Request::is('admin/individual/technician*') xor Request::is('admin/individuals/technician*')) active @endif" >
                    <a href="#"><span class="fa fa-wrench"></span><span class="xn-text">Technicians</span></a>
                    <ul>
                        <li @if(Request::is('admin/individuals/technician/active')) class="active" @endif>
                            <a href="/admin/individuals/technician/active"><span class="fa fa-check-square"></span><span class="xn-text">Active</span></a>
                        </li>
                        <li @if(Request::is('admin/individuals/suspended')) class="active" @endif>
                            <a href="/admin/individuals/technician/suspended"><span class="fa fa-minus-square"></span><span class="xn-text">Suspended</span></a>
                        </li>
                    </ul>
                </li>

            </ul>
        </li>


        @if(admin()->hasPermissionTo('View collaboration'))
            <li @if(Request::is('admin/collaborations') xor Request::is('admin/collaboration/*')) class="active" @endif>
                <a href="/admin/collaborations"><span class="fa fa-handshake-o"></span><span class="xn-text">Collaborations</span></a>
            </li>
        @endif

        @if(admin()->hasPermissionTo('View settings'))
            <li class="xn-openable @if(Request::is('admin/settings/*')) active @endif">
                <a href="#"><span class="xn-text"><span class="fa fa-cogs"></span> {{ __('language.Application Settings') }}</span></a>
                <ul>
                    @if(admin()->hasPermissionTo('View Address'))
                        <li @if(Request::is('admin/addresses/*')xor Request::is('admin/address/*')) class="active" @endif>
                            <a href="/admin/addresses/all"><span class="fa fa-flag"></span><span class="xn-text">Addresses</span></a>
                        </li>
                    @endif

                    <li @if(Request::is('admin/settings/about')) class="active" @endif>
                        <a href="/admin/settings/about"><span class="xn-text"><span class="fa fa-info-circle"></span>About Us</span></a>
                    </li>
                    <li @if(Request::is('admin/settings/terms')) class="active" @endif>
                        <a href="/admin/settings/terms"><span class="xn-text"><span class="fa fa-bars"></span>Terms</span></a>
                    </li>
                    <li @if(Request::is('admin/settings/privacy')) class="active" @endif>
                        <a href="/admin/settings/privacy"><span class="xn-text"><span class="fa fa-user-secret"></span>Privacy</span></a>
                    </li>
                    <li @if(Request::is('admin/settings/complains')) class="active" @endif>
                        <a href="/admin/settings/complains"><span class="xn-text"><span class="fa fa-list-alt"></span>Complains and Suggestions</span></a>
                    </li>

                </ul>
            </li>
        @endif

    </ul>
    <!-- END X-NAVIGATION -->
</div>
