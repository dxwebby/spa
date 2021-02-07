@extends('layouts.app')

@section('content')

<div class="wrapper">
    <!-- Sidebar blocker -->
    <div class="sidebar-blocker">
            
    </div>

    <!-- Sidebar  -->
    <nav id="sidebar">
        <div class="sidebar-header pl-2 mt-3">
            <h5><i class="fas fa-coins text-danger mr-1"></i> Budget Manager</h5>
        </div>

        <ul class="list-unstyled components">            
            <p>Navigation</p>
            <li class="active navLink">
                <router-link class="app_link" to="/"><img src="https://icongr.am/clarity/home.svg?size=18&color=e2342f" alt="dashboard"><span>Dashboard</span></router-link>                
            </li>    
            <li>
                <a href="#billsMenu" data-toggle="collapse" aria-expanded="false" class="dropdown-toggle"><img src="https://icongr.am/clarity/list.svg?size=18&color=e2342f" alt="expenses"><span>My Bills</span></a>
                <ul class="collapse list-unstyled navLink show" id="billsMenu">
                    <li class="navLink">                        
                        <router-link class="app_link" to="/addbill"><img src="https://icongr.am/clarity/copy.svg?size=18&color=e2342f" alt="dashboard"><span>Bills Management</span></router-link>                
                    </li>  
                    <li class="navLink">
                        <router-link class="app_link" to="/unlisted"><img src="https://icongr.am/clarity/eye-hide.svg?size=18&color=e2342f" alt="expenses"><span>Unlisted Payments</span></router-link>        
                    </li>                                                                          
                </ul>
            </li>                           
            <li>
                <a href="#homeSubmenu" data-toggle="collapse" aria-expanded="false" class="dropdown-toggle"><img src="https://icongr.am/clarity/briefcase.svg?size=18&color=e2342f" alt="expenses"><span>My Expenses</span></a>
                <ul class="collapse list-unstyled navLink show" id="homeSubmenu">
                    <li class="navLink">
                        <router-link class="app_link" to="/_myexpenses"><img src="https://icongr.am/clarity/file-group.svg?size=18&color=e2342f" alt="expenses"><span>Expenses</span></router-link>        
                    </li>                                                      
                    <li>                        
                        <router-link class="app_link" to="/unlisted_expenses"><img src="https://icongr.am/clarity/eye-hide.svg?size=18&color=e2342f" alt="expenses"><span>Unlisted Expenses</span></router-link>        
                    </li>                                                      
                </ul>
            </li>                                  
        </ul>  
        <ul class="list-unstyled components" style="margin-top: -15px;">
            <li class="navLink">
                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                    @csrf
                </form>            
                <a href="{{ route('logout') }}"
                        onclick="event.preventDefault();
                                        document.getElementById('logout-form').submit();">
                        <img src="https://icongr.am/clarity/logout.svg?size=18&color=e2342f" alt="logout"><span>Logout</span>
                </a>
            </li>
        </ul>     
    </nav>

    <!-- Page Content  -->
    <div id="content">
        <nav class="navbar navbar-expand-lg navbar-light bg-light">
            <div class="container-fluid p-0">
                
                <button class="navToggle space-collapsed pointx " id="sidebarCollapse" 
                        data-toggle="tooltip" data-placement="right"
                        data-html="true" title="<em>Toggle</em> <b>Sidebar</b>">
                    <div class="space-container">
                        <div class="space-bar"></div>
                        <div class="space-bar"></div>
                        <div class="space-bar"></div>                    
                    </div>
                </button>
                                
                <div class="collapse navbar-collapse text-center" id="navbarSupportedContent">                                        
                    <ul class="nav navbar-nav ml-auto">
                        <li class="nav-item">                            
                            <div class="nav-link welcome">
                                <span class="welcome">
                                    <i class="far fa-user"></i> Welcome back, {{Auth::user()->name}}!                                                                       
                                </span>
                            </div>                            
                        </li>    
                        <li class="nav-item">
                            <div class="nav-link">
                                <span class="welcome">
                                    <i class="far fa-calendar-alt"></i> {{ Carbon\Carbon::now('America/Chicago')->format('D, M d, Y')}}
                                </span>
                            </div>
                        </li>

                    </ul>
                </div>
            </div>
        </nav>

        <!-- api_token -->
        <input type="hidden" id="api_token" value="{{Auth::user()->api_token}}">        
        <input type="hidden" id="id" value="{{Auth::user()->id}}">        
        
        <!-- router-view -->    
        <!-- default view -->                  
        <div style="margin-top: -40px; margin-bottom: 100px;">                     
            <router-view></router-view>               
        </div> 
        <!-- /.router-view -->
    </div>
</div>
@endsection

@section('scripts')        

<script>


    
</script>

@endsection
