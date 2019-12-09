<template>
    <div>
    <div class="btn-container">
       <el-button type="primary" icon="el-icon-circle-plus" @click="openAddForm">Add Task</el-button>
        <!-- ADD Dialog -->
        <el-dialog title="Add Task" :visible.sync="dialogVisible" width="40%" :before-close="handleClose">
            <el-alert v-if="success"
                title="success alert"
                type="success" v-text="ruleForm.message">
            </el-alert>
            <el-alert v-if="validationError"
                title="error alert"
                type="error" v-text="validationError">
            </el-alert>
           
        <el-form :model="ruleForm" :rules="rules" ref="ruleForm" label-width="120px">
            <el-form-item label="Task Name" prop="name">
                <el-input v-model="ruleForm.name" placeholder="Task Name"></el-input>
            </el-form-item>
            <el-form-item label="Description" prop="description">
                <el-input v-model="ruleForm.description" placeholder="Description"></el-input>
            </el-form-item>
            <el-form-item label="Due Date">
                <el-date-picker type="date" prop="due" placeholder="Pick a date" v-model="ruleForm.due" style="width: 100%;" value-format="yyyy-MM-dd"></el-date-picker>
            </el-form-item>
            <el-form-item>
                <el-button type="primary" @click="onSubmit">Create</el-button>
                <el-button @click="resetForm('ruleForm')">Reset</el-button>
            </el-form-item>
         </el-form>
        </el-dialog>

        <!-- EDIT Dialog -->
        <el-dialog title="Edit Task" :visible.sync="dialogVisibleEdit" width="40%" :before-close="handleCloseEdit">
            <el-alert v-if="success"
                title="success alert"
                type="success">
            </el-alert>
        <el-form :model="ruleForm" :rules="rules" ref="ruleForm" label-width="120px">
            <el-form-item label="Task Name" prop="name">
                <el-input v-model="ruleForm.name" value="form.name" placeholder="Task Name"></el-input>
            </el-form-item>
            <el-form-item label="Description" prop="description">
                <el-input v-model="ruleForm.description" value="form.description" placeholder="Description"></el-input>
            </el-form-item>
            <el-form-item label="Due Date" prop="due">
                <el-date-picker type="date" placeholder="Pick a date" value="form.due" v-model="ruleForm.due" style="width: 100%;" value-format="yyyy-MM-dd"></el-date-picker>
            </el-form-item>
            <el-form-item>
                <el-button type="primary" @click="onSubmitEdit">Update</el-button>
                <el-button @click="dialogVisibleEdit=false;">Cancel</el-button>
            </el-form-item>
         </el-form>
        </el-dialog>
    </div>

    <!-- TABLE -->
        <el-table
        :data="tasks"
        style="width: 100%">
        <el-table-column
            prop="id"
            label="Task ID"
            width="180" sortable>
        </el-table-column>
        <el-table-column
            prop="name"
            label="Name"
            width="180" sortable>
        </el-table-column>
        <el-table-column
            prop="description"
            label="Description"
            width="180">
        </el-table-column>
        <el-table-column
            prop="due"
            label="Due Date" sortable>
        </el-table-column>
        <el-table-column
            prop="status"
            label="Status" sortable>
        </el-table-column>
        <el-table-column label="Action">
            <template slot-scope="scope">
                <el-button type="success" @click="taskDone(scope.row.id)" icon="el-icon-check" circle></el-button>
                <el-button type="primary" @click="taskEdit(scope.row.id)" icon="el-icon-edit" circle></el-button>
                <el-button type="danger" @click="taskDelete(scope.row.id)" icon="el-icon-delete" circle></el-button>
            </template>
         </el-table-column>
    </el-table>
    </div>
</template>

<script>
export default {
    mounted() {
        console.log("Mounted");
        axios.post('api/index', {}).then(response => {
            this.tasks = response.data;
        });
    },

    methods: {
        
        handleClose(done) {
            this.$confirm('Are you sure to close this dialog?').then(_ => {
                axios.post('api/index', {}).then(response => {
                    this.tasks = response.data;
                });
                done();
                
            }).catch(_ => {});
        },
        handleCloseEdit(done) {
            this.$confirm('Are you sure to close this dialog?').then(_ => {
                done();
                
            }).catch(_ => {});
        },
        openAddForm() {
            this.ruleForm.name = '';
            this.ruleForm.description = '';
            this.ruleForm.due = '';
            this.ruleForm.status = 'Pending';
            this.dialogVisible = true;
            this.success = false;
        },
         onSubmit() {
            this.$refs['ruleForm'].validate((valid) => {
                if(valid) {
                    axios.post('api/store', {
                        name: this.ruleForm.name,
                        description: this.ruleForm.description,
                        due: this.ruleForm.due,
                        status: this.ruleForm.status
                    }).then(response => {
                        this.ruleForm.message = response.data.message;
                        this.ruleForm.name = '';
                        this.ruleForm.description = '';
                        this.ruleForm.due = '';
                        this.ruleForm.status = 'Pending';
                        this.validationError = '';
                        this.success= true;
                        axios.post('api/index', {}).then(response => {
                            this.tasks = response.data;
                        });
                    }).catch(error => {
                        if (error.response.status == 422) {
                            this.validationError = error.response.data.errors.name[0];
                        }
                    });
                }else{
                    console.log("Error submit.");
                    return false;
                }

            });
        },
         onSubmitEdit() {
            this.$refs['ruleForm'].validate((valid) => {
                if(valid) {
                    axios.post('api/task-update/' + this.id, {
                        name: this.ruleForm.name,
                        description: this.ruleForm.description,
                        due: this.ruleForm.due,
                        id: this.id
                    }).then(response => {
                        this.ruleForm.message = response.data.message;
                        this.ruleForm.name = '';
                        this.ruleForm.description = '';
                        this.ruleForm.due = '';
                        this.ruleForm.status = 'Pending';
                        this.success= false;
                        this.id = null;
                        this.dialogVisibleEdit = false;
                        this.validationError = '';
                        axios.post('api/index', {}).then(response => {
                            this.tasks = response.data;
                        });
                    }).catch(error => {
                        if (error.response.status == 422) {
                            this.validationError = error.response.data.errors.name;
                        }
                    });
                }else{
                    console.log("Error submit!");
                    return false;
                }
            });
        },
        taskDone(taskID) {
           axios.post('api/task-done/' + taskID, {
               id: taskID
           }).then(response => {
                this.ruleForm.message = response.data.message;
                axios.post('api/index', {}).then(response => {
                    this.tasks = response.data;
                });
           });
        },
        taskEdit(taskID) {
            axios.post('api/task-edit/' + taskID, {
                id: taskID
            }).then(response=> {
                this.ruleForm.name = response.data.name;
                this.ruleForm.description = response.data.description;
                this.ruleForm.due = response.data.due;
                this.dialogVisibleEdit = true;
                this.id = taskID;
                this.success = false;
            });
        },
        taskDelete(taskID) {
            this.$confirm('Are you sure you want to delete the task?')
            .then(_ => {
                 axios.post('api/task-delete/' + taskID, {
                    id: taskID
                }).then(response => {
                    this.message = response.data.message;
                    axios.post('api/index', {}).then(response => {
                        this.tasks = response.data;
                    });
                });
                done();
            })
            .catch(_ => {});
        },

        resetForm(formName) {
            this.$refs[formName].resetFields();
        }
    },
    data: function() {
        return {
            dialogVisible: false,
            dialogVisibleEdit: false,
            tasks: null,
            ruleForm: {
                name: '',
                description: '',
                due: '',
                status: 'Pending'
            },
            validationError: '',
            success: false,
            id: null,
            scope: {
                row: {
                    id: ''
                }
            },
            rules: {
                name: [
                    { required: true, message: 'Please input task name', trigger: 'blur' },
                    { min: 3, message: 'Length should be minimum of 3 characters', trigger: 'blur' }
                ],
                description: [
                    { required: true, message: 'Please input task description', trigger: 'blur' },
                    { min: 3, message: 'Length of description must have a minimum of 3 characters', trigger: 'blur' }
                ],
                due: [
                    {required: true, message: 'Please pick a date', trigger: 'change' }
                ],
               
            }
        }
    }
}
</script>
<style>
    .btn-container {
        padding-bottom: 10px;
    }
    .btn-add-task {
        display:block;
        padding: auto 20px;
    }
    thead > th {
        text-align: center;
    }

    .fa-check, .fa-pencil {
        font-size: 15px;
    }

    .btn-done, .btn-pencil {
        padding: 10px;
       
    }
</style>