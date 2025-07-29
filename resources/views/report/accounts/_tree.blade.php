<h1>Accounts Tree</h1>

    @foreach($tree as $topGroupId => $data)
        <h2>{{ $data['group']->name }}</h2>
        <table class="table tree" id="tree-{{ $topGroupId }}">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Type</th>
                </tr>
            </thead>
            <tbody>
                <tr class="treegrid-g{{ $topGroupId }}">
                    <td>{{ $data['group']->name }}</td>
                    <td>Group</td>
                </tr>
                @foreach($data['tree'] as $node)
                    <tr class="treegrid-{{ $node['id'] }} treegrid-parent-{{ $node['parent'] }}">
                        <td>{{ $node['name'] }}</td>
                        <td>{{ $node['type'] }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endforeach