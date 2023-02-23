class Random 
{
    public static reverseafourintegerarry(int[] a) 
    {
        int temp;
        for (int i = 0; i < a.length / 2; i++) 
        {
            temp = a[i];
            a[i] = a[a.length - i - 1];
            a[a.length - i - 1] = temp;
        }
    }
    public static void main(String[] args) 
    {
        intarrayreverse(arr);
        System.print.println("Reversed Array: " + temp);
    }
    /*
    public static int intarrayreverse(int[] arr)
    {
        int[] arr1 = new int[arr.length];
        for(int i = 0; i < arr.length; i++)
        {
            arr1[i] = arr[arr.length - 1 - i];
        }
        return arr1;
    }
    */
}
