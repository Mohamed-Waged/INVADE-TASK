import React, { useState, useEffect } from 'react';
import axios from 'axios';

const ToDoList = () => {
  const [todos, setTodos] = useState([]);
  const [newTodo, setNewTodo] = useState('');
  const [editTodo, setEditTodo] = useState(null);

  // Fetch todos
  useEffect(() => {
    axios.get('http://your-laravel-api-url/api/todos')
      .then(response => setTodos(response.data))
      .catch(error => console.error(error));
  }, []);

  // Add a new to-do
  const handleAdd = () => {
    if (newTodo) {
      axios.post('http://your-laravel-api-url/api/todos', { title: newTodo })
        .then(response => {
          setTodos([...todos, response.data]);
          setNewTodo('');
        })
        .catch(error => console.error(error));
    }
  };

  // Edit a to-do
  const handleEdit = (todo) => {
    setEditTodo(todo);
  };

  const handleUpdate = () => {
    if (editTodo) {
      axios.put(`http://your-laravel-api-url/api/todos/${editTodo.id}`, { title: editTodo.title })
        .then(response => {
          setTodos(todos.map(todo => (todo.id === editTodo.id ? response.data : todo)));
          setEditTodo(null);
        })
        .catch(error => console.error(error));
    }
  };

  // Delete a to-do
  const handleDelete = (id) => {
    axios.delete(`http://your-laravel-api-url/api/todos/${id}`)
      .then(() => {
        setTodos(todos.filter(todo => todo.id !== id));
      })
      .catch(error => console.error(error));
  };

  return (
    <div>
      <h1>To-Do List</h1>

      {/* Add new to-do */}
      <div>
        <input
          type="text"
          value={newTodo}
          onChange={(e) => setNewTodo(e.target.value)}
          placeholder="Add a new to-do"
        />
        <button onClick={handleAdd}>Add</button>
      </div>

      {/* Edit to-do */}
      {editTodo && (
        <div>
          <input
            type="text"
            value={editTodo.title}
            onChange={(e) => setEditTodo({ ...editTodo, title: e.target.value })}
            placeholder="Edit to-do"
          />
          <button onClick={handleUpdate}>Update</button>
        </div>
      )}

      {/* Display to-do items */}
      <ul>
        {todos.map(todo => (
          <li key={todo.id}>
            {todo.title}
            <button onClick={() => handleEdit(todo)}>Edit</button>
            <button onClick={() => handleDelete(todo.id)}>Delete</button>
          </li>
        ))}
      </ul>
    </div>
  );
};

export default ToDoList;
