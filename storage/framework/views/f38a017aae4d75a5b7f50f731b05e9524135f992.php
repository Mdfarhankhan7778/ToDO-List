<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>To-Do List</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div id="app">
            <h1>To-Do List</h1>
            <input type="text" id="task-title" class="form-control" placeholder="Enter a new task" />
            <button id="add-task" class="btn btn-primary mt-2">Add Task</button>
            <button id="show-all" class="btn btn-secondary mt-2">Show All Tasks</button>
            <div class="mt-3">
                <div class="d-flex justify-content-between align-items-center font-weight-bold border-bottom pb-2">
                    <span>Task</span>
                    <span>Action</span>
                </div>
                <ul id="task-list" class="list-group"></ul>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const taskInput = document.getElementById('task-title');
            const addTaskButton = document.getElementById('add-task');
            const taskList = document.getElementById('task-list');
            const showAllButton = document.getElementById('show-all');

            // Load tasks
            const loadTasks = () => {
                axios.get('/tasks').then(response => {
                    taskList.innerHTML = ''; // Clear existing tasks
                    response.data.forEach(task => {
                        addTaskToList(task);
                    });
                }).catch(error => {
                    console.error('Error loading tasks:', error);
                });
            };

            // Add task to the list
            const addTaskToList = (task) => {
                const taskItem = document.createElement('li');
                taskItem.className = 'list-group-item d-flex justify-content-between align-items-center';
                taskItem.innerHTML = `
                    <span>${task.title}</span>
                    <div class="d-flex align-items-center">
                        <span class="me-2">${task.completed ? 'Completed' : 'Pending'}</span>
                        <input type="checkbox" class="me-2" ${task.completed ? 'checked' : ''}>
                        <button class="btn btn-danger btn-sm">Delete</button>
                    </div>
                `;

                const checkbox = taskItem.querySelector('input[type="checkbox"]');
                const statusLabel = taskItem.querySelector('span.me-2');
                checkbox.addEventListener('click', () => {
                    axios.put(`/tasks/${task.id}`, { completed: checkbox.checked }).then(() => {
                        taskItem.style.textDecoration = checkbox.checked ? 'line-through' : 'none';
                        statusLabel.textContent = checkbox.checked ? 'Completed' : 'Pending';
                    }).catch(error => {
                        console.error('Error updating task:', error);
                    });
                });

                const deleteButton = taskItem.querySelector('button');
                deleteButton.addEventListener('click', () => {
                    if (confirm('Are you sure to delete this task?')) {
                        axios.delete(`/tasks/${task.id}`).then(() => {
                            taskItem.remove();
                        }).catch(error => {
                            console.error('Error deleting task:', error);
                        });
                    }
                });

                taskItem.style.textDecoration = task.completed ? 'line-through' : 'none';

                taskList.appendChild(taskItem);
            };

            // Add task
            addTaskButton.addEventListener('click', () => {
                const title = taskInput.value.trim();
                if (title === '') {
                    alert('Task title cannot be empty');
                    return;
                }

                axios.post('/tasks', { title }).then(response => {
                    addTaskToList(response.data);
                    taskInput.value = '';
                }).catch(error => {
                    if (error.response && error.response.data.errors) {
                        alert('Task title must be unique');
                    } else {
                        console.error('Error adding task:', error);
                    }
                });
            });

            // Show all tasks
            showAllButton.addEventListener('click', loadTasks);

            // Initial load
            loadTasks();
        });

        // Set CSRF token for Axios
        axios.defaults.headers.common['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    </script>
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
</body>
</html>
<?php /**PATH D:\xampp\htdocs\todo-app\resources\views/welcome.blade.php ENDPATH**/ ?>