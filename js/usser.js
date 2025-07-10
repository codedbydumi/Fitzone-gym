{/* <script>
// Sample user data
let users = [
    { id: 1, name: 'John Doe', email: 'john.doe@example.com' },
    { id: 2, name: 'Jane Smith', email: 'jane.smith@example.com' }
];

// Handle modal visibility
const userModal = document.getElementById('userModal');
const addUserButton = document.getElementById('addUserButton');
const closeModalButton = document.querySelector('.close-button');
const userForm = document.getElementById('userForm');
const userList = document.getElementById('user-list');
const formFeedback = document.getElementById('formFeedback');

// Show modal for adding new user
addUserButton.addEventListener('click', () => {
    userModal.style.display = 'block';
    clearForm();
});

// Close modal when "X" is clicked
closeModalButton.addEventListener('click', () => {
    userModal.style.display = 'none';
    clearForm();
});

// Close modal if clicked outside modal content
window.addEventListener('click', (event) => {
    if (event.target === userModal) {
        userModal.style.display = 'none';
        clearForm();
    }
});

// Clear form inputs
function clearForm() {
    userForm.reset();
    formFeedback.style.visibility = 'hidden';
}

// Add user to the list
function addUser(user) {
    users.push(user);
    displayUsers();
    userModal.style.display = 'none';
}

// Display all users in the user list
function displayUsers() {
    userList.innerHTML = ''; // Clear current list

    users.forEach(user => {
        const userItem = document.createElement('li');
        userItem.classList.add('user-item');
        
        userItem.innerHTML = `
            <span>${user.name} - ${user.email}</span>
            <button class="edit-button" data-id="${user.id}">Edit</button>
            <button class="delete-button" data-id="${user.id}">Delete</button>
        `;
        
        // Add event listeners to Edit and Delete buttons
        userItem.querySelector('.edit-button').addEventListener('click', (e) => editUser(e));
        userItem.querySelector('.delete-button').addEventListener('click', (e) => deleteUser(e));

        userList.appendChild(userItem);
    });
}

// Edit user details
function editUser(event) {
    const userId = parseInt(event.target.getAttribute('data-id'));
    const user = users.find(user => user.id === userId);
    
    if (user) {
        document.getElementById('userName').value = user.name;
        document.getElementById('userEmail').value = user.email;
        // Change the form's submit button action to update the user
        userForm.onsubmit = (e) => {
            e.preventDefault();
            updateUser(userId);
        };
        userModal.style.display = 'block';
    }
}

// Update user details in the list
function updateUser(userId) {
    const user = users.find(user => user.id === userId);
    if (user) {
        user.name = document.getElementById('userName').value;
        user.email = document.getElementById('userEmail').value;
        displayUsers();
        userModal.style.display = 'none';
    }
}

// Delete user from the list
function deleteUser(event) {
    const userId = parseInt(event.target.getAttribute('data-id'));
    users = users.filter(user => user.id !== userId);
    displayUsers();
}

// Handle form submission (Add New User)
userForm.addEventListener('submit', (e) => {
    e.preventDefault();
    
    const userName = document.getElementById('userName').value;
    const userEmail = document.getElementById('userEmail').value;
    const userPassword = document.getElementById('userPassword').value;
    
    // Check if all fields are filled
    if (userName && userEmail && userPassword) {
        const newUser = {
            id: users.length + 1,  // Simple ID generation
            name: userName,
            email: userEmail,
            password: userPassword // In a real app, hash this password!
        };
        addUser(newUser);
        formFeedback.style.visibility = 'hidden';  // Hide error feedback
    } else {
        formFeedback.textContent = 'Please fill in all fields.';
        formFeedback.style.visibility = 'visible';
    }
});

// Initial call to display the users on page load
document.addEventListener('DOMContentLoaded', () => {
    displayUsers();
});
</script> */}