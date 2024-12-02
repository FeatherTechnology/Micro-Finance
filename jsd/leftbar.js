$(document).ready(function () {

    $.post('api/base_api/menulist.php', function (response) {
        if (response.length != 0) {
            // Call the function with the response
            createSidebarMenu(response);
            // Dropdown menu
            $(".sidebar-dropdown > a").click(function () {
                $(".sidebar-submenu").slideUp(200);
                if ($(this).parent().hasClass("active")) {
                    $(".sidebar-dropdown").removeClass("active");
                    $(this).parent().removeClass("active");
                } else {
                    $(".sidebar-dropdown").removeClass("active");
                    $(this).next(".sidebar-submenu").slideDown(200);
                    $(this).parent().addClass("active");
                }
            });
        }
    }, 'json')



    // Define the mapping of current_page values to current_module values
    const moduleMapping = {
        'company_creation_list': 'master',
        'company_creation': 'master',
        'branch_creation_list': 'master',
        'branch_creation': 'master',
        'loan_category_creation_list': 'master',
        'loan_category_creation': 'master',
        'bank_creation_list': 'admin',
        'bank_creation': 'admin',
        'agent_creation_list': 'admin',
        'agent_creation': 'admin',
        'user_creation_list': 'admin',
        'user_creation': 'admin',
        'loan_entry_list': 'loan_entry',
        'loan_entry': 'loan_entry',
        'approval_list': 'approval',
        'approval': 'approval',
        'loan_issue_list': 'loan_issue',
        'loan_issue': 'loan_issue',
        'collection_list': 'collection',
        'collection': 'collection',
        'closed_list': 'closed',
        'closed': 'closed',
        'noc_list': 'noc',
        'noc': 'noc',
        'accounts': 'accounts',
        'update_customer_list': 'update',
        'update_customer': 'update',
        'update_document_list': 'update',
        'update_document': 'update',
        'customer_data_list': 'customer_data',
        'customer_data': 'customer_data',
        'search_list': 'search',
        'search': 'search',
        'reports_list': 'reports',
        'reports': 'reports',
    };

    const current_page = localStorage.getItem('currentPage');
    // Assign the current_module based on the current_page value
    const current_module = moduleMapping[current_page] || 'dashboard';

    // Call the function with the current module
    setTimeout(() => {
        toggleSidebarSubmenus(current_module);
    }, 1000);

})

// Function to create the sidebar menu
function createSidebarMenu(response) {
    var sidebar = $('<ul></ul>');

    // Group submenus by main menu
    var grouped = {};
    response.forEach(function (item) {
        if (!grouped[item.main_menu]) {
            grouped[item.main_menu] = [];
        }
        grouped[item.main_menu].push(item);
    });
    // Create main menu items
    for (var mainMenu in grouped) {
        var mainMenuLi = $('<li class="sidebar-dropdown ' + grouped[mainMenu][0].main_menu_link + '"></li>');
        var mainMenuLink = $('<a href="javascript:void(0)"></a>').appendTo(mainMenuLi);
        mainMenuLink.append('<i class="icon-' + grouped[mainMenu][0].main_menu_icon + '"></i>');
        mainMenuLink.append('<span class="menu-text">' + mainMenu + '</span>');

        var submenuDiv = $('<div class="sidebar-submenu"></div>').appendTo(mainMenuLi);
        var submenuUl = $('<ul></ul>').appendTo(submenuDiv);

        // Create submenu items
        grouped[mainMenu].forEach(function (subItem) {
            var subLi = $('<li></li>').appendTo(submenuUl);
            var subLink = $('<a href="' + subItem.sub_menu_link + '"></a>').appendTo(subLi);
            subLink.append('<i class="icon-' + subItem.sub_menu_icon + '"></i>');
            subLink.append(subItem.sub_menu);

        });

        sidebar.append(mainMenuLi);
    }

    // Append the sidebar to the DOM
    $('.sidebar-menu').append(sidebar);
}

function toggleSidebarSubmenus(current_module) {
    // Find all elements with the class 'sidebar-submenu'
    var sidebarSubmenus = document.querySelectorAll('.sidebar-submenu');

    // Loop through each submenu
    sidebarSubmenus.forEach(function (submenu) {
        // Check if the parent <li> has the class that matches the current module
        var parentLi = submenu.closest('li');
        if (parentLi && parentLi.classList.contains(current_module)) {
            // If it matches, show the submenu
            submenu.style.display = 'block';
        } else {
            // If it doesn't match, hide the submenu
            submenu.style.display = 'none';
        }
    });

    var sidebarLinks = document.querySelectorAll('.page-wrapper .sidebar-wrapper .sidebar-menu .sidebar-dropdown .sidebar-submenu ul li a');
    let current_page = localStorage.getItem('currentPage');

    sidebarLinks.forEach(function (link) {
        var href = link.getAttribute('href');
        if (href === current_page) {
            link.style.backgroundColor = '#646969d9';
        }
    });
    if (current_page == 'dashboard') {
        $('.dashboard').css('backgroundColor', '#646969d9');
    }
}