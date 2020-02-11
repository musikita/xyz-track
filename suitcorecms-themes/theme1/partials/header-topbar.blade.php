                            <!--begin: Search -->

                            <!--begin: Search -->
                            <div class="kt-header__topbar-item kt-header__topbar-item--search dropdown" id="kt_quick_search_toggle">
                                <div class="kt-header__topbar-wrapper" data-toggle="dropdown" data-offset="10px,0px">
                                    <span class="kt-header__topbar-icon">
                                        <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1" class="kt-svg-icon">
                                            <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                                <rect id="bound" x="0" y="0" width="24" height="24" />
                                                <path d="M14.2928932,16.7071068 C13.9023689,16.3165825 13.9023689,15.6834175 14.2928932,15.2928932 C14.6834175,14.9023689 15.3165825,14.9023689 15.7071068,15.2928932 L19.7071068,19.2928932 C20.0976311,19.6834175 20.0976311,20.3165825 19.7071068,20.7071068 C19.3165825,21.0976311 18.6834175,21.0976311 18.2928932,20.7071068 L14.2928932,16.7071068 Z" id="Path-2" fill="#000000" fill-rule="nonzero" opacity="0.3" />
                                                <path d="M11,16 C13.7614237,16 16,13.7614237 16,11 C16,8.23857625 13.7614237,6 11,6 C8.23857625,6 6,8.23857625 6,11 C6,13.7614237 8.23857625,16 11,16 Z M11,18 C7.13400675,18 4,14.8659932 4,11 C4,7.13400675 7.13400675,4 11,4 C14.8659932,4 18,7.13400675 18,11 C18,14.8659932 14.8659932,18 11,18 Z" id="Path" fill="#000000" fill-rule="nonzero" />
                                            </g>
                                        </svg> </span>
                                </div>
                                <div class="dropdown-menu dropdown-menu-fit dropdown-menu-right dropdown-menu-anim dropdown-menu-lg">
                                    <div class="kt-quick-search kt-quick-search--inline" id="kt_quick_search_inline">
                                        <form method="get" class="kt-quick-search__form">
                                            <div class="input-group">
                                                <div class="input-group-prepend"><span class="input-group-text"><i class="flaticon2-search-1"></i></span></div>
                                                <input type="text" class="form-control kt-quick-search__input" placeholder="Search...">
                                                <div class="input-group-append"><span class="input-group-text"><i class="la la-close kt-quick-search__close"></i></span></div>
                                            </div>
                                        </form>
                                        <div class="kt-quick-search__wrapper kt-scroll" data-scroll="true" data-height="300" data-mobile-height="200">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!--end: Search -->

                            <!--end: Search -->
@if (auth()->user() && ($cmsUserNotificationRoute = config('suitcorecms.users.notification')))
                            <!--begin: Notifications -->
                            <div class="kt-header__topbar-item dropdown">
                                <div class="kt-header__topbar-wrapper" data-toggle="dropdown" data-offset="30px,0px" aria-expanded="true">
                                    <span class="kt-header__topbar-icon kt-pulse kt-pulse--brand">
                                        <i class="fa fa-bell kt-font-primary"></i>
                                        <span class="kt-pulse__ring"></span>
                                    </span>            
                                    <!--
                                        Use dot badge instead of animated pulse effect: 
                                        <span class="kt-badge kt-badge--dot kt-badge--notify kt-badge--sm kt-badge--brand"></span>
                                    -->
                                </div>
                                <div class="dropdown-menu dropdown-menu-fit dropdown-menu-right dropdown-menu-anim dropdown-menu-top-unround dropdown-menu-lg">
                                        <div>
                                            <!--begin: Head -->
                                            <div class="kt-head kt-head--skin-dark kt-head--fit-x kt-head--fit-b" style="background-image: url({{ cms_asset() }}media/misc/bg-1.jpg)">
                                                <h3 class="kt-head__title">
                                                    User Notifications
                                                    &nbsp;
                                                    <a href="{{route($cmsUserNotificationRoute)}}" class="btn btn-link btn-success btn-sm btn-bold btn-font-md">See All</a>
                                                </h3>

                                                <div class="nav nav-tabs nav-tabs-line nav-tabs-bold nav-tabs-line-3x nav-tabs-line-success kt-notification-item-padding-x" role="tablist">
                                                </div>
                                            </div>
                                        <!--end: Head -->

                                        <div class="tab-content">
                                            <div class="tab-pane active show" id="topbar_notifications_notifications" role="tabpanel">
                                                <div class="kt-notification kt-margin-t-10 kt-margin-b-10 kt-scroll" data-scroll="true" data-height="300" data-mobile-height="200">
                                                    @if (count($meNotifications = (new \App\Models\MeNotification)->notificationList()))
                                                    @foreach ($meNotifications as $item)
                                                    {!! show_notification_item($item) !!}
                                                    @endforeach
                                                    @else
                                                    <div class="kt-grid kt-grid--ver" style="min-height: 200px;">
                                                        <div class="kt-grid kt-grid--hor kt-grid__item kt-grid__item--fluid kt-grid__item--middle">
                                                            <div class="kt-grid__item kt-grid__item--middle kt-align-center">
                                                                All caught up!
                                                                <br>No new notifications.
                                                            </div>
                                                        </div>
                                                    </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <!--end: Notifications -->
@endif

                            <!--begin: User Bar -->
                            <div class="kt-header__topbar-item kt-header__topbar-item--user">
                                <div class="kt-header__topbar-wrapper" data-toggle="dropdown" data-offset="0px,0px">
                                    <div class="kt-header__topbar-user">
                                        <span class="kt-header__topbar-welcome kt-hidden-mobile">Hi,</span>
                                        <span class="kt-header__topbar-username kt-hidden-mobile">{{$cmsUserEmail = auth(config('suitcorecms.guard'))->user()->email ?? '(Not Logged)'}}</span>
                                        <img class="kt-hidden" alt="Pic" src="{{ cms_asset() }}media/users/300_25.jpg" />

                                        <!--use below badge element instead the user avatar to display username's first letter(remove kt-hidden class to display it) -->
                                        <span class="kt-badge kt-badge--username kt-badge--unified-success kt-badge--lg kt-badge--rounded kt-badge--bold">{{$initialName = substr($cmsUserEmail, 0, 1)}}</span>
                                    </div>
                                </div>
                                <div class="dropdown-menu dropdown-menu-fit dropdown-menu-right dropdown-menu-anim dropdown-menu-top-unround dropdown-menu-xl">

                                    <!--begin: Head -->
                                    <div class="kt-user-card kt-user-card--skin-dark kt-notification-item-padding-x" style="background-image: url({{ cms_asset() }}media/misc/bg-1.jpg)">
                                        <div class="kt-user-card__avatar">
                                            <img class="kt-hidden" alt="Pic" src="{{ cms_asset() }}media/users/300_25.jpg" />

                                            <!--use below badge element instead the user avatar to display username's first letter(remove kt-hidden class to display it) -->
                                            <span class="kt-badge kt-badge--lg kt-badge--rounded kt-badge--bold kt-font-success">{{$initialName}}</span>
                                        </div>
                                        <div class="kt-user-card__name">
                                            {{$cmsUserEmail}}
                                        </div>
                                    </div>

                                    <!--end: Head -->

                                    <!--begin: Navigation -->
                                    <div class="kt-notification">


                                        <div class="kt-notification__custom kt-space-between">
                                            @if ($cmsLogout = config('suitcorecms.users.logout'))
                                            <a href="{{ route($cmsLogout) }}" class="btn btn-label btn-label-brand btn-sm btn-bold">Sign Out</a>
                                            @endif
                                            @if ($cmsUserRoute = config('suitcorecms.users.profile'))
                                            <a href="{{route($cmsUserRoute)}}" class="btn btn-success btn-sm btn-bold btn-font-md">My Profile</a>
                                            @endif
                                        </div>
                                    </div>

                                    <!--end: Navigation -->
                                </div>
                            </div>

                            <!--end: User Bar -->
                        </div>