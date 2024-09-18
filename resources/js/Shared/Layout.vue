<template>

    <div class="mdk-drawer-layout js-mdk-drawer-layout" data-push data-responsive-width="992px">
        <div class="mdk-drawer-layout__content page-content">
            <!-- Header -->
            <!-- Navbar -->
            <div class="navbar navbar-expand pr-0 navbar-light border-bottom-2" id="default-navbar" data-primary>
                <!-- Navbar Toggler -->
                <button class="navbar-toggler w-auto mr-16pt d-block d-lg-none rounded-0" type="button" data-toggle="sidebar" id="navBtn">
                    <span class="material-icons">short_text</span>
                </button>
                <!-- // END Navbar Toggler -->
                <div class="flex"></div>
                <!-- Navbar Menu -->
                <div class="nav navbar-nav flex-nowrap d-flex mr-16pt">
                    <!-- Notifications dropdown -->
<!--                    <div class="nav-item ml-16pt dropdown dropdown-notifications dropdown-xs-down-full" data-toggle="tooltip" data-title="Notifications" data-placement="bottom" data-boundary="window">-->
<!--                        <button class="nav-link btn-flush dropdown-toggle" type="button" data-toggle="dropdown" data-caret="false">-->
<!--                            <i class="material-icons">notifications_none</i>-->
<!--                            <span class="badge badge-notifications badge-accent">2</span>-->
<!--                        </button>-->
<!--                        <div class="dropdown-menu dropdown-menu-right">-->
<!--                            <div data-perfect-scrollbar class="position-relative">-->
<!--                                <div class="dropdown-header">-->
<!--                                    <strong>System notifications</strong>-->
<!--                                </div>-->
<!--                                <div class="list-group list-group-flush mb-0">-->
<!--                                    <a href="javascript:void(0);" class="list-group-item list-group-item-action unread">-->
<!--                      <span class="d-flex align-items-center mb-1">-->
<!--                        <small class="text-black-50">3 minutes ago</small>-->
<!--                        <span class="ml-auto unread-indicator bg-accent"></span>-->
<!--                      </span>-->
<!--                                        <span class="d-flex">-->
<!--                        <span class="avatar avatar-xs mr-2">-->
<!--                          <span class="avatar-title rounded-circle bg-light">-->
<!--                            <i class="material-icons font-size-16pt text-accent">account_circle</i>-->
<!--                          </span>-->
<!--                        </span>-->
<!--                        <span class="flex d-flex flex-column">-->
<!--                          <span class="text-black-70">Your profile information has not been synced correctly.</span>-->
<!--                        </span>-->
<!--                      </span>-->
<!--                                    </a>-->
<!--                                    <a href="javascript:void(0);" class="list-group-item list-group-item-action">-->
<!--                      <span class="d-flex align-items-center mb-1">-->
<!--                        <small class="text-black-50">5 hours ago</small>-->
<!--                      </span>-->
<!--                                        <span class="d-flex">-->
<!--                        <span class="avatar avatar-xs mr-2">-->
<!--                          <span class="avatar-title rounded-circle bg-light">-->
<!--                            <i class="material-icons font-size-16pt text-primary">group_add</i>-->
<!--                          </span>-->
<!--                        </span>-->
<!--                        <span class="flex d-flex flex-column">-->
<!--                          <strong class="text-black-100">Adrian. D</strong>-->
<!--                          <span class="text-black-70">Wants to join your private group.</span>-->
<!--                        </span>-->
<!--                      </span>-->
<!--                                    </a>-->
<!--                                    <a href="javascript:void(0);" class="list-group-item list-group-item-action">-->
<!--                      <span class="d-flex align-items-center mb-1">-->
<!--                        <small class="text-black-50">1 day ago</small>-->
<!--                      </span>-->
<!--                                        <span class="d-flex">-->
<!--                        <span class="avatar avatar-xs mr-2">-->
<!--                          <span class="avatar-title rounded-circle bg-light">-->
<!--                            <i class="material-icons font-size-16pt text-warning">storage</i>-->
<!--                          </span>-->
<!--                        </span>-->
<!--                        <span class="flex d-flex flex-column">-->
<!--                          <span class="text-black-70">Your deploy was successful.</span>-->
<!--                        </span>-->
<!--                      </span>-->
<!--                                    </a>-->
<!--                                </div>-->
<!--                            </div>-->
<!--                        </div>-->
<!--                    </div>-->
                    <!-- // END Notifications dropdown -->
                    <div class="nav-item dropdown">
                        <a href="#" class="nav-link d-flex align-items-center dropdown-toggle" data-toggle="dropdown" data-caret="false">
                        <span class="avatar avatar-sm mr-8pt2">
                            <span class="avatar-title rounded-circle">{{ initials(auth.fname) }}{{ initials(auth.lname) }}</span>
                        </span>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right">
                            <div class="dropdown-header">
                                <strong>Account</strong>
                            </div>
                            <Link class="dropdown-item" :href="'/'+url+'/'+auth.id+'/edit'">Edit Account</Link>
                            <Link class="dropdown-item" :href="'/'+url+'/password'">Change Password</Link>
                            <Link class="dropdown-item" href="/logout" method="post" as="button">Logout</Link>
                        </div>
                    </div>
                </div>
                <!-- // END Navbar Menu -->
            </div>
            <!-- // END Navbar -->
            <!-- // END Header -->
            <!-- BEFORE Page Content -->
            <!-- // END BEFORE Page Content -->
            <!-- Page Content -->
            <!-- Replace this with your page content -->
                <slot/>
            <!-- // END Replace this with your page content -->
            <!-- // END Page Content -->
            <!-- Footer -->
            <div class="bg-white border-top-2 mt-auto">
                <div class="container page__container page-section d-flex flex-column">
                    <p class="mb-8pt d-flex">
                        <a href="" class="text-70 text-underline mr-8pt small">Terms</a>
                        <a href="" class="text-70 text-underline small">Privacy policy</a>
                    </p>
                    <p class="text-50 small mt-n1 mb-0">Copyright {{ yearLabel() }} &copy;. Tarlac Agricultural University, Camiling, Tarlac. All rights reserved. Version 1.03</p>
                </div>
            </div>
            <!-- // END Footer -->
        </div>
        <!-- // END drawer-layout__content -->
        <!-- drasssswer -->
        <div class="mdk-drawer js-mdk-drawer" id="default-drawer">
            <div class="mdk-drawer__content">
                <div class="sidebar sidebar-dark-pickled-bluewood sidebar-left" data-perfect-scrollbar>
                    <!-- Sidebar Content -->
                    <Link href="/" class="sidebar-brand ">
                        <span>
                            <span>
                                <img src="/images/tau.webp" class="img-fluid" alt="logo" />
                            </span>
                        </span>
                    </Link>
                    <div class="sidebar-heading">{{ auth.role }}</div>
                    <ul class="sidebar-menu">
                        <li class="sidebar-menu-item" :class="{'active': $page.component == 'Faculty/Profile' || $page.component == 'Student/Profile'}">
                            <Link class="sidebar-menu-button" href="/">
                                <span class="material-icons sidebar-menu-icon sidebar-menu-icon--left">home</span>
                                <span class="sidebar-menu-text">Dashboard</span>
                            </Link>
                        </li>
                       
<!--                        <li class="sidebar-menu-item" v-if="auth.role != 1" :class="{'active': $page.component == 'Student/StudentList'}">-->
<!--                            <Link class="sidebar-menu-button" href="/student">-->
<!--                                <span class="material-icons sidebar-menu-icon sidebar-menu-icon&#45;&#45;left">school</span>-->
<!--                                <span class="sidebar-menu-text">Manage Students</span>-->
<!--                            </Link>-->
<!--                        </li>-->
                        <li v-if="auth.permission.includes('view all faculty')" class="sidebar-menu-item" @click="toggleFaculty" 
                        :class="$page.component == 'Quiz/All' || $page.component == 'Lesson/All' || $page.component == 'Subject/All' || activeFaculty || $page.component == 'Faculty/FacultyList' ? 'open' : ''">
                            <a class="sidebar-menu-button js-sidebar-collapse" data-toggle="collapse" href="javascript:void(0);" >
                                <span class="material-icons sidebar-menu-icon sidebar-menu-icon--left">format_shapes</span> Manage Faculty <span class="ml-auto sidebar-menu-toggle-icon"></span>
                            </a>
                            <ul class="sidebar-submenu sm-indent collapse" id="tau_menu">
                                <li class="sidebar-menu-item" :class="{'active': $page.component == 'Faculty/FacultyList'}">
                                    <Link class="sidebar-menu-button" href="/faculty">
                                        <span class="sidebar-menu-text">All Faculty</span>
                                    </Link>
                                </li>
                                <li v-if="auth.permission.includes('view all subject')" class="sidebar-menu-item" :class="{'active': $page.component == 'Subject/All'}">
                                    <Link class="sidebar-menu-button" href="/subject/all">
                                        <span class="sidebar-menu-text">All Courses</span>
                                    </Link>
                                </li>
                                <li v-if="auth.permission.includes('view all lesson')" class="sidebar-menu-item" :class="{'active': $page.component == 'Lesson/All'}">
                                    <Link class="sidebar-menu-button" href="/lesson/all">
                                        <span class="sidebar-menu-text">All Lessons</span>
                                    </Link>
                                </li>
                                <li v-if="auth.permission.includes('create all quiz')" class="sidebar-menu-item" :class="{'active': $page.component == 'Quiz/All'}">
                                    <Link class="sidebar-menu-button" href="/quiz/all">
                                        <span class="sidebar-menu-text">All Quiz</span>
                                    </Link>
                                </li>
                            </ul>
                        </li>
                        <!-- <li class="sidebar-menu-item" :class="active || $page.component == 'Subject/SubjectList' || $page.component == 'Subject/SubjectIndex' ||
                        $page.component == 'Lesson/Create' || $page.component == 'Lesson/Edit' || $page.component == 'Lesson/LessonList' ||
                        $page.component == 'Quiz/Create' || $page.component == 'Quiz/Edit' || $page.component == 'Quiz/QuizList' ||
                        $page.component == 'Student/StudentSubjectList'
                         ? 'open' : ''" @click="toggle">
                            <a class="sidebar-menu-button js-sidebar-collapse" data-toggle="collapse" href="javascript:void(0);" >
                                <span class="material-icons sidebar-menu-icon sidebar-menu-icon--left">import_contacts</span> Manage Classes <span class="ml-auto sidebar-menu-toggle-icon"></span>
                            </a>
                            <ul class="sidebar-submenu sm-indent collapse" id="tau_menu">
                                <li class="sidebar-menu-item" :class="{'active': $page.component == 'Subject/SubjectList' || $page.component == 'Subject/SubjectIndex' || $page.component == 'Student/StudentSubjectList'}">
                                    <Link class="sidebar-menu-button" href="/subject">
                                        <span class="sidebar-menu-text">Courses</span>
                                    </Link>
                                </li>
                                <li class="sidebar-menu-item" v-if="auth.permission.includes('view lesson dashboard')" :class="{ 'active' : $page.component == 'Lesson/Create' || $page.component == 'Lesson/Edit' || $page.component == 'Lesson/LessonList'}">
                                    <Link class="sidebar-menu-button" href="/lesson">
                                        <span class="sidebar-menu-text">Lesson</span>
                                    </Link>
                                </li>
                                <li class="sidebar-menu-item" v-if="auth.permission.includes('view quiz dashboard')" :class="{ 'active' : $page.component == 'Quiz/Create' || $page.component == 'Quiz/Edit' || $page.component == 'Quiz/QuizList'}">
                                    <Link class="sidebar-menu-button" href="/quiz">
                                        <span class="sidebar-menu-text">Quiz</span>
                                    </Link>
                                </li>
                            </ul>
                        </li> -->
                        <li class="sidebar-menu-item" :class="{'active': 
                        $page.component == 'Subject/SubjectList' ||
                         $page.component == 'Subject/SubjectIndex' || 
                         $page.component == 'Student/StudentSubjectList' ||
                         $page.component == 'Lesson/Index' || 
                         $page.component == 'Lesson/Create' || 
                         $page.component == 'Lesson/Edit' ||
                         $page.component == 'Quiz/Index' ||
                         $page.component == 'Quiz/Create' ||
                         $page.component == 'Quiz/Edit' ||
                         $page.component == 'Student/Index'
                         }">
                            <Link class="sidebar-menu-button" href="/subject">
                                <span class="material-icons sidebar-menu-icon sidebar-menu-icon--left">school</span>
                                <span class="sidebar-menu-text">Course</span>
                            </Link>
                        </li>
                        <li class="sidebar-menu-item" :class="{'active': $page.component == 'ProfileEdit'}">
                            <Link class="sidebar-menu-button" :href="'/'+url+'/'+auth.id+'/edit/'">
                                <span class="material-icons sidebar-menu-icon sidebar-menu-icon--left">tune</span>
                                <span class="sidebar-menu-text">Profile Settings</span>
                            </Link>
                        </li>

                        <li class="sidebar-menu-item" v-if="auth.permission.includes('view student report') && auth.role=='student'" :class="{'active': $page.component == 'Report/Index'}">
                            <Link class="sidebar-menu-button" href="/report">
                                <span class="material-icons sidebar-menu-icon sidebar-menu-icon--left">poll</span>
                                <span class="sidebar-menu-text">Report Grade</span>
                            </Link>
                        </li>
                        <li class="sidebar-menu-item" v-if="auth.permission.includes('add time records')" :class="{'active': $page.component == 'Recorder/Recorder'}">
                            <Link class="sidebar-menu-button" href="/recorder">
                                <span class="material-icons sidebar-menu-icon sidebar-menu-icon--left">radio_button_checked</span>
                                <span class="sidebar-menu-text">Time Records</span>
                            </Link>
                        </li>
                        <li class="sidebar-menu-item" v-if="auth.permission.includes('manage roles')" :class="{'active': $page.component == 'Roles/Role' || $page.component == 'Roles/Create' || $page.component == 'Roles/Edit'}">
                            <Link class="sidebar-menu-button" href="/roles">
                                <span class="material-icons sidebar-menu-icon sidebar-menu-icon--left">verified_user</span>
                                <span class="sidebar-menu-text">Manage Roles</span>
                            </Link>
                        </li>
                        <li class="sidebar-menu-item" v-if="auth.permission.includes('manage database')" :class="{'active': $page.component == 'Maintenance/Index'}">
                            <Link class="sidebar-menu-button" href="/maintenance">
                                <span class="material-icons sidebar-menu-icon sidebar-menu-icon--left">settings</span>
                                <span class="sidebar-menu-text">Maintenance</span>
                            </Link>
                        </li>
                    </ul>
                    <ScreenRecord :videoId="flash.message" v-if="auth.permission.includes('add time records')"/>
                    <!-- // END Sidebar Content -->
                </div>
            </div>
        </div>
        <!-- // END drawer -->
    </div>
</template>


<script setup>
import ScreenRecord from './ScreenRecord.vue';
let props = defineProps({
    auth: Object,
    flash: Object,
})
let url;

if(props.auth.url == 'student'){
    url = 'student';
}else{
    url = 'faculty';
}

function initials(data){
    const name = data;
    return `${name[0].charAt(0)}`;
}

</script>
<script>
export default {
    data() {
        return {
            active: false,
            activeFaculty: false,
        }
    },
    methods:{
        toggle(){
            this.active = ! this.active;
        },
        toggleFaculty(){
            this.activeFaculty = ! this.activeFaculty;
        },
        yearLabel(){
            return new Date().getFullYear();
        }
    }
}

</script>
<style>
.sidebar-dark-pickled-bluewood .sidebar-heading, .sidebar-dark-pickled-bluewood .sidebar-heading>a {
    color: #c2f2e0;
}
[dir] .sidebar-dark-pickled-bluewood .open {
    background: #2d7056;
}
.sidebar-dark-pickled-bluewood .open>.sidebar-menu-button {
    color: #c2f2e0;
}
.sidebar-dark-pickled-bluewood .open>.sidebar-menu-button .sidebar-menu-icon {
    color: #c2f2e0;
}
.sidebar-dark-pickled-bluewood .sidebar-submenu .sidebar-menu-button {
    color: #c2f2e0;
}
[dir=ltr] .sidebar-dark-pickled-bluewood .sidebar-submenu .sidebar-menu-text {
    border-left: 1px solid #c2f2e0;
}
[dir] .sidebar-dark-pickled-bluewood .sidebar-submenu .sidebar-menu-text::after {
    background-color: #EFF2C2;
}
</style>

