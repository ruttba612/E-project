 <!-- Add/Edit Product Modal -->
    <div class="modal fade" id="addProductModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">Add Product</h5>
                    <button class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <form method="POST" enctype="multipart/form-data" id="productForm">
                        <input type="hidden" name="id" id="edit-id">
                        <div class="form-group">
                            <label>Name</label>
                            <input type="text" name="name" id="edit-name" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>Category</label>
                            <select name="category_id" id="edit-category_id" class="form-control" required>
                                <?php
                                $conn = new mysqli('localhost', 'root', '', 'auranest_db');
                                if ($conn->connect_error) {
                                    echo "<option value=''>Database error: " . htmlspecialchars($conn->connect_error) . "</option>";
                                } else {
                                    $result = $conn->query("SELECT id, name FROM categories WHERE id IN (2, 3)");
                                    while ($row = $result->fetch_assoc()) {
                                        echo "<option value='{$row['id']}'>" . htmlspecialchars($row['name']) . "</option>";
                                    }
                                    $conn->close();
                                }
                                ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Price</label>
                            <input type="number" name="price" id="edit-price" class="form-control" step="0.01" min="0" required>
                        </div>
                        <div class="form-group">
                            <label>Stock</label>
                            <input type="number" name="stock" id="edit-stock" class="form-control" min="0" required>
                        </div>
                        <div class="form-group">
                            <label>Description</label>
                            <textarea name="description" id="edit-description" class="form-control" rows="4" required></textarea>
                        </div>
                        <div class="form-group">
                            <label>Status</label>
                            <select name="status" id="edit-status" class="form-control" required>
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Image</label>
                            <input type="file" name="image" id="edit-image" class="form-control-file" accept="image/*">
                            <div id="imagePreview" style="margin-top: 10px;"></div>
                        </div>
                        <button type="submit" name="add_product" class="btn btn-custom btn-add">Add Product</button>
                        <button type="submit" name="edit_product" id="edit-submit" class="btn btn-custom btn-add" style="display:none;">Save Changes</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteConfirmModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Confirm Delete</h5>
                    <button class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    Are you sure you want to delete this product? This action cannot be undone.
                </div>
                <div class="modal-footer">
                    <button class="btn btn-cancel" data-dismiss="modal">Cancel</button>
                    <button class="btn btn-confirm" id="confirmDelete">Yes, Delete</button>
                </div>
            </div>
        </div>
    </div>

    