let btn_bar = document.querySelector('#menu-btn');
let menu_bar = document.querySelector('.header-container .flex .menu-bar');
if (btn_bar) {
    btn_bar.addEventListener('click', () => {
        profile_div.classList.remove('active');
        menu_bar.classList.toggle('active');
    });
}


let btn_user = document.querySelector('#user-btn');
let profile_div = document.querySelector('.header-container .flex .profile');
if (btn_user) {
    btn_user.addEventListener('click', () => {
        profile_div.classList.toggle('active');
        menu_bar.classList.remove('active');
    })
}


window.onscroll = () => {
    if (menu_bar)
        menu_bar.classList.remove('active');
    if (profile_div)
        profile_div.classList.remove('active');
}

let gadget_select = document.querySelector('.gadget-select');
if (gadget_select) {
    let c1 = document.querySelector('.div-laptop');
    let c2 = document.querySelector('.div-smartphone');
    let c3 = document.querySelector('.div-smartwatch');
    let c4 = document.querySelector('.div-accessory');
    gadget_select.addEventListener('change', () => {
        if (gadget_select.value === 'laptop') {
            c1.classList.remove('hidden');
            c2.classList.add('hidden');
            c3.classList.add('hidden');
            c4.classList.add('hidden');
        }
        else if (gadget_select.value === 'smartphone') {
            c1.classList.add('hidden');
            c2.classList.remove('hidden');
            c3.classList.add('hidden');
            c4.classList.add('hidden');
        }
        else if (gadget_select.value === 'smartwatch') {
            c1.classList.add('hidden');
            c2.classList.add('hidden');
            c3.classList.remove('hidden');
            c4.classList.add('hidden');
        }
        else {
            c1.classList.add('hidden');
            c2.classList.add('hidden');
            c3.classList.add('hidden');
            c4.classList.remove('hidden');
        }
    })
}

let clear_btn = document.querySelector('.addgg-clear')
if (clear_btn) {
    clear_btn.addEventListener('click', (event) => {
        event.preventDefault(); // Prevent the default behavior

        // Reset all input fields
        document.querySelectorAll('input').forEach(input => {
            if (input.type === 'file') {
                input.value = ''; // Clear file input
            } else if (input.type === 'number' || input.type === 'text' || input.type === 'date') {
                input.value = ''; // Clear text, number, and date inputs
            }
        });

        // Reset select options
        document.querySelectorAll('select').forEach(select => {
            select.selectedIndex = 0; // Set to the first option
        });
    });
}
