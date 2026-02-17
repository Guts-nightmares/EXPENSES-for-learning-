<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Dépenses</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-8">
    <div class="max-w-3xl mx-auto">
        <h1 class="text-2xl font-bold text-gray-800 mb-6">Gestion des Dépenses</h1>

        <div class="bg-white p-4 mb-4">
            <h2 class="font-bold text-gray-700 mb-2">Ce mois</h2>
            <p class="text-xl" id="monthlyTotal">0.00 €</p>
        </div>

        <div class="bg-white p-4 mb-4">
            <h2 class="font-bold text-gray-700 mb-2">Nouvelle dépense</h2>
            <form id="expenseForm" class="flex flex-col gap-2">
                <input type="text" id="description" placeholder="Description" required
                    class="border p-2 rounded">
                <input type="number" id="amount" placeholder="Montant (€)" step="0.01" required
                    class="border p-2 rounded">
                <select id="category" required class="border p-2 rounded">
                    <option value="">Catégorie</option>
                    <option value="alimentation">Alimentation</option>
                    <option value="transport">Transport</option>
                    <option value="loyer">Loyer</option>
                    <option value="loisirs">Loisirs</option>
                    <option value="sante">Santé</option>
                    <option value="vetements">Vêtements</option>
                    <option value="autres">Autres</option>
                </select>
                <button type="submit" class="bg-blue-500 text-white p-2 rounded hover:bg-blue-600">
                    Ajouter
                </button>
            </form>
        </div>

        <div class="bg-white p-4 mb-4">
            <h2 class="font-bold text-gray-700 mb-2">Par catégorie</h2>
            <div id="categoryStats" class="flex flex-wrap gap-2"></div>
        </div>

        <div class="bg-white p-4">
            <h2 class="font-bold text-gray-700 mb-2">Historique</h2>
            <table class="w-full">
                <thead>
                    <tr class="text-left border-b">
                        <th class="p-2">Date</th>
                        <th class="p-2">Description</th>
                        <th class="p-2">Catégorie</th>
                        <th class="p-2 text-right">Montant</th>
                        <th class="p-2"></th>
                    </tr>
                </thead>
                <tbody id="expenseList"></tbody>
            </table>
            <p id="emptyState" class="text-gray-500 text-center p-4 hidden">Aucune dépense</p>
        </div>
    </div>

    <script>
        const API_URL = '../src/Controllers/ExpenseController.php';

        async function fetchExpenses() {
            const res = await fetch(`${API_URL}?stats=1`);
            return res.json();
        }

        async function addExpense(desc, amount, cat) {
            await fetch(API_URL, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ description: desc, amount, category: cat })
            });
        }

        async function deleteExpense(id) {
            await fetch(API_URL, {
                method: 'DELETE',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `id=${id}`
            });
        }

        function formatDate(dateStr) {
            return new Date(dateStr).toLocaleDateString('fr-FR');
        }

        function formatAmount(amount) {
            return parseFloat(amount).toFixed(2) + ' €';
        }

        async function render() {
            const data = await fetchExpenses();
            
            document.getElementById('monthlyTotal').textContent = 
                formatAmount(data.monthlyTotal?.total || 0);

            document.getElementById('categoryStats').innerHTML = data.byCategory.map(cat => `
                <span class="bg-gray-100 px-2 py-1 rounded text-sm">
                    ${cat.category} - ${formatAmount(cat.total)}
                </span>
            `).join('');

            const list = document.getElementById('expenseList');
            const empty = document.getElementById('emptyState');
            
            if (data.expenses.length === 0) {
                list.innerHTML = '';
                empty.classList.remove('hidden');
            } else {
                empty.classList.add('hidden');
                list.innerHTML = data.expenses.map(exp => `
                    <tr class="border-b hover:bg-gray-50">
                        <td class="p-2 text-gray-600">${formatDate(exp.date)}</td>
                        <td class="p-2">${exp.description}</td>
                        <td class="p-2 text-sm text-gray-500">${exp.category}</td>
                        <td class="p-2 text-right text-red-600">-${formatAmount(exp.amount)}</td>
                        <td class="p-2 text-center">
                            <button onclick="handleDelete(${exp.id})" class="text-red-500">X</button>
                        </td>
                    </tr>
                `).join('');
            }
        }

        document.getElementById('expenseForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            const desc = document.getElementById('description').value;
            const amount = document.getElementById('amount').value;
            const cat = document.getElementById('category').value;

            await addExpense(desc, amount, cat);
            e.target.reset();
            render();
        });

        async function handleDelete(id) {
            if (confirm('Supprimer ?')) {
                await deleteExpense(id);
                render();
            }
        }

        render();
    </script>
</body>
</html>
