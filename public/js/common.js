toastr.options = {
    "closeButton": true,
    "progressBar": true,
};

link_selector();

function link_selector() {
    let route_uri = window.location.pathname.slice(1);
    let uri_arr = route_uri.split('/');
    // console.log(route_uri);
    // console.log(uri_arr);
    const collapse_link = document.querySelector(`.${uri_arr[0]}_collapse_link`);
    if (uri_arr.length > 1 && collapse_link !== null) {
        let link = null;
        for (let link_text of uri_arr) {
            link = document.querySelector(`.${link_text}_link`);
            if (link !== null) {
                break;
            }
        }
        if (link) {
            link.parentElement.classList.add('active');
        }
        collapse_link.classList.add('active');
        collapse_link.parentElement.classList.add('menu-open');
    }
}

function number_format(currency_code, number) {
    const formatter = Intl.NumberFormat('en-US', {
        style: "currency",
        currency: currency_code,
    })
    return formatter.format(number);
}

function default_datetime(datetime) {
    const date = new Date(datetime);
    let customDate = date.getDay();
    return customDate;
}

function init_client_datatables(table_id) {
    $(`#${table_id}`).DataTable({
        pageLength: 10,
        language: {
            "search": "_INPUT_", // Removes the 'Search' field label
            "searchPlaceholder": "Type here to search..", // Placeholder for the search box
            processing: `<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i><span class="sr-only">Loading...</span>`, // loader
        },
        order: [0, 'desc']
    });
}

function handle_select_all(elem, target_elem_className) {
    const target_elem = document.querySelectorAll(target_elem_className);
    if (elem.checked) {
        for (let item of target_elem) {
            item.checked = true;
        }
    } else {
        for (let item of target_elem) {
            item.checked = false;
        }
    }
}

//Global function
function handle_view_message(value = "N/A") {
    const div = document.createElement('div');
    div.classList = "modal fade";
    div.id = "viewMessageModal"
    div.setAttribute("tabindex", "-1");
    const modal_elem = `<div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Message</h4>
                <button type="button" class="btn text-secondary" onclick="handle_remove_modal('viewMessageModal')" data-bs-dismiss="modal" aria-label="Close"><i class="fa fa-times"></i></button>
            </div>
            <div class="modal-body text-sm">
                <div class="container">
                    ${value}
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-secondary" onclick="handle_remove_modal('viewMessageModal')" data-bs-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>`;
    div.innerHTML = modal_elem;
    document.body.appendChild(div);
    const modal = new bootstrap.Modal(div);
    modal.show();
}

function handle_remove_modal(modal_id) {
    const current_modal = document.getElementById(modal_id);
    current_modal.remove();
}

function btn_preloader(elem) {
    const loader = `<i class="fa fa-spinner fa-spin custom_spinner_icon"></i>`;
    const btnText = elem.innerText;
    setTimeout(() => {
        elem.innerHTML += loader;
        elem.disabled = true;
    }, 200);
}

function handle_sidebar_collapse() {
    const sidebar_collapsible_elem = document.getElementById('sidebar_collapsible_elem');
    const action_value = sidebar_collapsible_elem.getAttribute('data-collapse');
    if (action_value == 0) {
        console.log("triggred to expand");
        sidebar_collapsible_elem.setAttribute('data-collapse', 1); // 1 means: expand
        localStorage.setItem('sidebar_collapse', false);
    } else {
        console.log("triggred to collapse");
        localStorage.setItem('sidebar_collapse', true);
        sidebar_collapsible_elem.setAttribute('data-collapse', 0); // 0 means: collapse
    }
}


function validate_mobile_number(elem, $request_url) {
    const pattern = /^\d{10}$/;
    const error_message_elem = elem.nextElementSibling;
    if (!elem.value.match(pattern)) {
        error_message_elem.classList.remove('d-none');
        elem.classList.add('border-danger');
        return false;
    } else {
        elem.classList.remove('border-danger');
        error_message_elem.classList.add('d-none');
    }
    fetch($request_url).then(response => response.json()).then(data => {
        console.log(data);
        if (data.success == false) {
            error_message_elem.classList.remove('d-none');
            elem.classList.add('border-danger');
            toastr.error(`${data.message}`);
        } else {
            elem.classList.remove('border-danger');
            error_message_elem.classList.add('d-none');
        }
    })
}

function validate_email(elem) {
    const pattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    const error_message_elem = elem.nextElementSibling;
    if (!elem.value.match(pattern) && elem.value != '') {
        error_message_elem.classList.remove('d-none');
        elem.classList.add('border-danger');
    } else {
        elem.classList.remove('border-danger');
        error_message_elem.classList.add('d-none');
    }
}

function integer_validate(elem) {
    const pattern = /^\d+$/;
    const error_message_elem = elem.nextElementSibling;
    if (!elem.value.match(pattern) && elem.value != '') {
        error_message_elem.classList.remove('d-none');
        elem.classList.add('border-danger');
    } else {
        elem.classList.remove('border-danger');
        error_message_elem.classList.add('d-none');
    }
}

