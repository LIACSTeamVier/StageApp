<!-- admin sidebar -->

<style>

.sidebar {
    position: fixed;
    margin-top: -10px;
    margin-left: -10px;
    padding-bottom: -10px;
    min-height: 100vh;
    width: 120px;
    background-color: #001158;
}

.menu {
    padding-top: 20px;
    padding-left: 10px;
}

.menu-item a {
    color: white;
    text-decoration: none;
    font-family: "Verdana";
    font-size: 12px;
}

.dropdown {
    display: none;
    position: absolute;
    background-color: #22337a;
    margin-left: 110px;
    min-width: 120px;
    box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.2);
}

.dropdown a {
    color: white;
    text-decoration: none;
    font-family: "Verdana";
    font-size: 12px;
}

.show {
    display: block;
}

</style>

<div class="sidebar">
    <div class="menu">
        <div class="menu-item">
            <h4><a href="#">Overview</a></h4>
        </div>
        <div class="dropdown" id="dropdown">
                <div><a href="#">Internship contact</a></div>
                <div><a href="#">Supervisor</a></div>
                <div><a href="#">Student</a></div>
                <div><a href="#">Administrator</a></div>
        </div>
        <div class="menu-item">
            <h4><a href="#" onclick="toggleDropdown()">Create account</a></h4>
        </div>
        <div class="menu-item">
            <h4><a href="#">Students</a></h4>
        </div>
        <div class="menu-item">
            <h4><a href="#">Supervisors</a></h4>
        </div>
        <div class="menu-item">
            <h4><a href="#">Projects</a></h4>
        </div>
        <div class="menu-item">
            <h4><a href="#">Logout</a></h4>
        </div>
    </div> 
</div>

<script>
function toggleDropdown() {
    document.getElementById("dropdown").classList.toggle("show");
}
</script>  