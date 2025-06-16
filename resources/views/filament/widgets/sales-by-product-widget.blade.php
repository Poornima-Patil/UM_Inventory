<x-filament::widget>
    <x-filament::card>
        <h2 class="text-lg font-bold mb-4 text-primary-700">Sales by Product</h2>
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm border border-gray-200 rounded-lg overflow-hidden">
                <thead>
                    <tr class="bg-primary-100 text-primary-800">
                        <th class="text-left px-4 py-2 border border-gray-200">Product</th>
                        <th class="text-right px-4 py-2 border border-gray-200">Total</th>
                        <th class="text-right px-4 py-2 border border-gray-200">Cash</th>
                        <th class="text-right px-4 py-2 border border-gray-200">UPI</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $grandTotal = 0;
                        $grandCash = 0;
                        $grandUpi = 0;
                    @endphp
                    @forelse ($sales as $row)
                        @php
                            $grandTotal += $row['total'];
                            $grandCash += $row['cash'];
                            $grandUpi += $row['upi'];
                        @endphp
                        <tr class="even:bg-gray-50 hover:bg-primary-50 transition">
                            <td class="px-4 py-2 font-medium border border-gray-200">{{ $row['product'] }}</td>
                            <td class="text-right px-4 py-2 text-green-700 font-semibold border border-gray-200">₹ {{ number_format($row['total'], 2) }}</td>
                            <td class="text-right px-4 py-2 text-blue-700 border border-gray-200">₹ {{ number_format($row['cash'], 2) }}</td>
                            <td class="text-right px-4 py-2 text-purple-700 border border-gray-200">₹ {{ number_format($row['upi'], 2) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center py-4 text-gray-400 border border-gray-200">No sales data available.</td>
                        </tr>
                    @endforelse
                    <tr class="bg-primary-200 font-bold">
                        <td class="px-4 py-2 text-right border border-gray-200">Total</td>
                        <td class="text-right px-4 py-2 text-green-900 border border-gray-200">₹ {{ number_format($grandTotal, 2) }}</td>
                        <td class="text-right px-4 py-2 text-blue-900 border border-gray-200">₹ {{ number_format($grandCash, 2) }}</td>
                        <td class="text-right px-4 py-2 text-purple-900 border border-gray-200">₹ {{ number_format($grandUpi, 2) }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </x-filament::card>
</x-filament::widget>
