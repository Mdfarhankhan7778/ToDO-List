document.addEventListener('DOMContentLoaded', function() {
    fetchTasks();
});

function fetchTasks() {
    fetch('/api/tasks')
        .then(response => response.json())
        .then(tasks => {
            const taskList = document.getElementById('task-list');
            taskList.innerHTML = '';

            tasks.forEach(task => {
                const taskItem = document.createElement('li');
                taskItem.setAttribute('data-id', task.id);
                
                const checkbox = document.createElement('input');
                checkbox.type = 'checkbox';
                checkbox.checked = task.completed;
                checkbox.onchange = () => toggleComplete(task.id, !task.completed);

                const span = document.createElement('span');
                span.textContent = task.task;
                span.style.textDecoration = task.completed ? 'line-through' : 'none';

                const deleteButton = document.createElement('button');
                deleteButton.textContent = 'Delete';
                deleteButton.onclick = () => confirmDelete(task.id);

                taskItem.appendChild(checkbox);
                taskItem.appendChild(span);
                taskItem.appendChild(deleteButton);
                
                taskList.appendChild(taskItem);
            });
        });
}

function addTask() {
    const newTaskInput = document.getElementById('new-task');
    const newTask = newTaskInput.value.trim();

    if (newTask === '') return;

    fetch('/api/tasks', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
        },
        body: JSON.stringify({ task: newTask })
    })
    .then(response => response.json())
    .then(task => {
        newTaskInput.value = '';
        fetchTasks();
    })
    .catch(error => {
        alert('Task already exists or an error occurred!');
    });
}

function toggleComplete(taskId, completed) {
    fetch(`/api/tasks/${taskId}`, {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
        },
        body: JSON.stringify({ completed: completed })
    })
    .then(() => {
        fetchTasks();
    });
}

function confirmDelete(taskId) {
    if (confirm('Are you sure to delete this task?')) {
        deleteTask(taskId);
    }
}

function deleteTask(taskId) {
    fetch(`/api/tasks/${taskId}`, {
        method: 'DELETE',
        headers: {
            'Accept': 'application/json',
        }
    })
    .then(() => {
        fetchTasks();
    });
}
